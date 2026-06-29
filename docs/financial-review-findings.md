# Financial-core review — findings & remediation checklist

A multi-agent correctness review of the tax/accounting/compliance core (tax engine,
payroll, customer/supplier invoicing, journal/ledger, financial statements, DGI/DSF
export). **39 issues raised, 32 confirmed, 7 refuted.** Each tax claim was independently
recomputed against Cameroon ground-truth rates.

Tax constants used as ground truth: TVA 17.5% of HT · CAC = 10% of TVA = 1.75% of HT ·
TTC = HT × 1.1925 · Précompte 5.5% (DGE/CIME) · CNPS 2.8% salarial / 11.2% patronal,
plafond 750 000 XAF · IRPP progressive + CAC 10% of IRPP · XAF = whole francs.

---

## ✅ Already fixed (software bugs, no tax rate changed)

| # | File | What was wrong | Fix |
|---|------|----------------|-----|
| A | `FinancialStatementService::aggregateLines` | Statements aggregated **all** journal entries incl. PENDING/FAILED/REVERSED | Filter `transaction_status = 'SUCCESSFUL'` (matches every other report) |
| B | `DgiFiscalisExportController` | Balance générale included **soft-deleted** entries | Exclude `deleted_at` |
| C | `PayrollController` (+ `total_rav` column) | **Payroll could never post** — entry unbalanced by total RAV | Accrue `total_rav`, credit it to 447000, round TSR to whole XAF — debits now == credits |
| D | `CustomerInvoiceController::recordWithholding`, `CustomerCreditNoteController::store` | Journal posted with wrong keys (`entry_date`/`reference`/`source`) → NULL posting_date / invalid source_pipeline → couldn't post | Use `posting_date`/`reference_id`/`memo`/`source_pipeline='MANUAL_INVOICE'` |
| E | `SupplierInvoiceController::store` | Failed post left an orphaned DRAFT invoice | Wrap create+post in a DB transaction |

---

## ✅ Implemented (standard Cameroon rules — VERIFY figures vs current Loi de Finances)

1. ✅ **IRPP 500 000 abattement applied** — `CnpsIrppService` now does
   `(annualGross − CNPS) × 0.70 − 500 000`. IRPP dropped accordingly (e.g. 200k/mo salary:
   13 608 → 9 441/mo). *Confirm the abattement still applies & the amount.*
2. ✅ **RAV progressive scale** — replaced the flat 7 500/yr with a monthly salary-bracket
   table (`RAV_BRACKETS`). **⚠ Verify the bracket amounts** — the structure is right, the
   figures are the published scale and live in one editable constant.
4. ✅ **`recordWithholding` clamped** — net_receivable can no longer go negative; amounts
   rounded to whole XAF. *(The statutory-base validation still depends on #3 below.)*
5. ✅ **IRPP brackets made contiguous** — no more 1-XAF gap at each threshold.

Customer-invoice GL posting + DGI/DSF (also implemented):
- ✅ **Customer invoices post to the GL on send** — `markSent` now posts
  Dr 411100 / Cr 701100 / Cr 443100 / Cr 448600 and links the journal entry (verified
  balanced: HT 100k → TTC 119 250). Control accounts standardized to 411100/701100.
- ✅ **DGI sync includes CAC** (amount_cac + in tax total/TTC).
- ✅ **DSF irpp_retenu** no longer bundles supplier précompte (separate line).

## ⚖️ STILL NEEDS CONFIRMATION — tax rules

3. **Précompte base — HT or TTC?** `FiscalGeographyRouter.php:52-54`. The one I won't guess.
   Tell me the legal base for the 5.5% and I'll wire it (incl. the `recordWithholding` check).
6. **Tax rounding scale** — `CameroonTaxEngine.php:19` computes to 2 decimals; XAF has no
   centimes. Confirm DGI per-line vs per-invoice rounding before switching to whole francs. *(MEDIUM)*
7. **`reverseFromTtc` doesn't round-trip** — `CameroonTaxEngine.php:47`. Confirm which figure
   DGI treats as authoritative on inclusive amounts. *(MEDIUM)*
8. **DSF `cnps_salarie`** reads combined 431000 — needs the payroll posting to split employee
   vs employer CNPS into distinct accounts. *(MEDIUM)*

---

## 🧾 NEEDS CONFIRMATION — SYSCOHADA account mapping / accounting structure

> These are real defects but the correct fix depends on your chart-of-accounts mapping.

8. **Customer invoices never post revenue/TVA/CAC to the GL** —
   `CustomerInvoiceController` (store/creditNote/recordWithholding). Sales aren't booked, yet
   credit-notes/withholding post reversals against those un-booked accounts. Recommended: on
   finalize, post `Dr 411100 TTC / Cr 701100 HT / Cr 443100 TVA / Cr 448600 CAC` and store the
   journal_entry_id. Confirm the posting lifecycle (on send? on payment?). *(HIGH)*
9. **Supplier CAC breaks double-entry** — `SupplierInvoiceService.php:45-60`. Invoices with
   `cac_amount > 0` can't balance (credit includes CAC, no matching debit). Need the SYSCOHADA
   treatment of purchase CAC (recoverable input tax vs expensed) to add the balancing debit. *(HIGH)*
10. **Balance sheet double-counts Class 1** — `FinancialStatementService.php:200-226`. Class 1
    credit balances land in **both** liabilities and equity, so the balanced check is almost
    always false. Partition: equity = 10–15, financial debt = 16–18. *(HIGH)*
11. **Balance sheet classifies Class 2/5 by sign only** — `:185-213`. Fixed assets / treasury
    can be mislabelled as liabilities; should net contra-accounts (28x/29x/39x/49x/59x). *(MEDIUM)*
12. **Inconsistent customer control accounts** — `creditNote` uses 411100/701100 while
    `CustomerCreditNoteController::store` uses 411000/701000. Standardize. *(MEDIUM)*

---

## 🏛️ NEEDS CONFIRMATION — DGI / DSF compliance export

13. **DGI invoice sync omits CAC entirely** — `SyncInvoiceToDgiPortalJob.php:48-72`.
    Under-reports tax & TTC to the portal. Add a CAC bucket (448600); confirm the payload schema
    (is CAC a separate field?). *(HIGH)*
14. **DSF `irpp_retenu` adds supplier précompte (447100) to salary IRPP (447000)** —
    `DsfExportService.php:72`. Report them as separate DSF lines. *(HIGH)*
15. **DSF `cnps_salarie` reads combined 431000** (employee + employer) — overstates the
    employee share. Use `total_cnps_employee` or split the accounts. *(MEDIUM)*
16. **DGI HT base only sums 701100/706000** — `SyncInvoiceToDgiPortalJob.php:51-58`. Misses
    other class-70 revenue on multi-line invoices. *(MEDIUM)*

---

## 🔧 Remaining software fixes (no tax judgment)

- ✅ **Idempotency** — a PAID invoice can no longer be credit-noted twice, and withholding
  can only be recorded once per invoice (was double-reversing TVA). *Fixed.*
- ✅ **Inactive account → 500** — `ManualJournalController` now rejects inactive account codes
  at validation (422) instead of 500-ing inside the posting transaction. *Fixed.*
- ✅ **DGI sync re-send** — `SyncInvoiceToDgiPortalJob` now atomically claims an entry
  (PENDING/REJECTED → SYNCING in one conditional UPDATE) so concurrent workers can't
  double-télétransmit and an APPROVED entry is never re-sent. *Fixed.*
- ⏳ **Float money**: several spots round XAF to 2 decimals / use float sums (reports, ledger
  PDF, supplier invoicing, DGI export). **Coupled to the tax-engine rounding scale (items 6–7
  above)** — summing as whole francs only makes sense once the per-amount scale is confirmed,
  so this is held with the tax-rule items rather than changed blindly. *(MEDIUM)*

---

## ❌ Refuted (checked, not real issues)

`numeric` validation accepts scientific notation · credit-limit ignores credit notes ·
`taxRate()`/`splitInputVat` base-rate confusion · unbounded Grand-Livre PDF query ·
ADJUSTMENT immutability not enforced at posting · aged-payables GROUP BY non-portable.
