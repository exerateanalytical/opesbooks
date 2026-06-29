# Tenant-API security review — findings & status

Multi-agent review of the tenant-facing API for tenant isolation, authorization,
validation and correctness. **40 confirmed, 0 refuted.** 23 were cross-tenant data
leaks (IDOR). The root cause was systemic: the `companies/{company}` route group never
verified the authenticated user belonged to the `{company}` in the URL.

---

## ✅ Fixed (verified live)

- **Systemic IDOR — the whole `companies/{company}` group** *(HIGH, ~15 endpoints:
  customers, quotations, delivery notes, suppliers, POs, stock, budgets, ledger,
  invoices, payroll, etc.)*. New `EnsureCompanyAccess` middleware verifies the caller
  is a member of (or active in) the `{company}` param. Verified: own company → 200,
  cross-tenant → 403.
- **CompanyController** *(HIGH)* — `index()` returned **every** company on the platform
  (verified: owner now sees 1 of 5); `show/update/destroy` were IDOR. Now scoped /
  membership-checked.
- **FixedAsset `runDepreciation` & Recurring `runNow`** *(HIGH)* — both posted journals
  for **all tenants**, ignoring `{company}`. Now scoped to the caller's company (services
  take an optional `companyId`; a scheduler can still run all tenants).
- **OfflineSync push/pull/status** *(HIGH)* — resolved the company from a request-body
  `company_niu` with no ownership check (cross-tenant read/write). Now verifies membership.
  Verified: own NIU → 200, other → 403.

---

## ⚖️ Needs a design decision (documented, not rushed)

- **Firm `addClient` lets any firm attach ANY company** *(HIGH)* —
  `FirmController.php:180-225`. A firm user can attach an arbitrary `company_id` to their
  portfolio and gain full access to that tenant's books, with **no client consent**. Needs
  a client-authorization / invitation flow (the company's OWNER must approve) before a firm
  can onboard it. This is a product decision, not a one-line fix.
- **Firm `searchCompanies` enumerates every tenant** *(HIGH)* — `FirmController.php:359-381`
  leaks name/NIU/tax_regime/subscription_status of all companies. Tie to the same
  consent model (only show companies that invited the firm), or restrict to exact-NIU lookup.
- **ChartOfAccounts mutates the GLOBAL shared chart** *(MEDIUM)* —
  `ChartOfAccountsController.php:22-53`. `store`/`update` write to `syscohada_accounts`
  (shared by all tenants) — a tenant can rename a standard account for everyone. Proper fix:
  add a nullable `company_id` (null = standard/read-only, set = tenant's custom account) and
  scope store/update/index to it.

---

## 🔧 Remaining lower-severity items (clear fixes, can do on request)

Cross-tenant FK injection — `exists` rules not company-scoped (a user can only inject into
their own company now that the middleware is in place, so these are data-integrity, not
read-leaks):
- DeliveryNote `customer_id/supplier_id/invoice/PO` *(was high → now medium)* — `:32-35`
- PurchaseOrder `supplier_id` — `:32,60`
- Project `client_id` — `:58-77`
- CRM lead `assigned_to` — `:74,98`
- Onboarding `addInvoice` `customer_id` — `OnboardingController.php:80`
- BankReconciliation match to another tenant's journal line — `:96-101`

Correctness / validation:
- Document numbers (quotation, delivery note, PO, project code) use a global `count()+1`
  against a globally-unique column → collisions / cross-tenant volume leakage.
- Quotation status machine allows illegal transitions; `convert()` doesn't require ACCEPTED.
- Stock OUT uses request-supplied unit cost instead of weighted-average (CMUP); allows
  negative stock; PO over-receipt beyond ordered qty.
- BankStatement `parseAmount()` corrupts grouped/US-format amounts; unparseable dates post
  to today; `skip_rows` default drops the first row.
- Recurring `update` can set end_date before start_date.
- DataImport commit is non-atomic with no per-row error isolation.
- Firm: `closeClient` can strand a user with `company_id = null`; `lockPeriod` unbounded;
  `report` runs an N+1 per-client query.
