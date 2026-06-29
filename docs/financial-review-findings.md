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

## ⚖️ NEEDS YOUR / YOUR ACCOUNTANT'S CONFIRMATION — tax rules

> These change a **rate, base, or bracket**. Getting them "fixed" wrong just produces a
> different wrong filing, so they are deliberately **not** changed yet. Confirm each against
> the current Code Général des Impôts / Loi de Finances, then I'll implement.

1. **IRPP omits the 500 000 XAF abattement** — `CnpsIrppService.php:46-47`. Net taxable is
   `(annualGross − CNPS) × 0.70` with **no** lump-sum deduction. If the standard 500 000
   abattement applies, IRPP is currently **overstated** for every employee. *(HIGH)*
2. **RAV is a flat 7 500/yr for everyone** — `CnpsIrppService.php:18,54`. Should be the
   progressive salary-bracket scale. Need the official bracket table. *(HIGH)*
3. **Précompte computed on HT, not TTC** — `FiscalGeographyRouter.php:52-54`. Confirm the
   legal base for the 5.5% withholding. *(HIGH)*
4. **`recordWithholding` trusts a client-supplied amount** — `CustomerInvoiceController.php:302-333`.
   No check against the statutory 5.5% base; should validate/clamp and stop net_receivable
   going negative. *(MEDIUM, rate/base part)*
5. **IRPP bracket boundaries use `min+1`** — drops 1 XAF of base at each threshold. *(LOW)*
6. **Tax rounding scale** — `CameroonTaxEngine.php:19` computes to 2 decimals; XAF has no
   centimes. Confirm DGI per-line vs per-invoice rounding rule before switching to whole francs. *(MEDIUM)*
7. **`reverseFromTtc` doesn't round-trip** — `CameroonTaxEngine.php:47`. TTC→HT→TTC can lose
   francs; confirm which figure DGI treats as authoritative on inclusive amounts. *(MEDIUM)*

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
- ⏳ **DGI sync re-send**: `SyncInvoiceToDgiPortalJob` has no row lock / status guard against
  concurrent or post-certification re-transmission (it does early-return on APPROVED). *(MEDIUM)*
- ⏳ **Float money**: several spots round XAF to 2 decimals / use float sums (reports, ledger
  PDF, supplier invoicing, DGI export) — should be integer/BigDecimal. *(MEDIUM)*

---

## ❌ Refuted (checked, not real issues)

`numeric` validation accepts scientific notation · credit-limit ignores credit notes ·
`taxRate()`/`splitInputVat` base-rate confusion · unbounded Grand-Livre PDF query ·
ADJUSTMENT immutability not enforced at posting · aged-payables GROUP BY non-portable.
