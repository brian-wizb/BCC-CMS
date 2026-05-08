# BCC CMS Feature Migration Phases

This document tracks the phased migration of features from the Next.js (BCC_CMS_PROJECT) to the Laravel (bcc-cms) project.

---

## Phase 1: Core Finance Modules
- Payroll management (pages & API)
- Expenditure management (pages & API)
- Department income/expenses (pages & API)
- Income types/records (pages & API)

## Phase 2: Giving & Campaigns
- Donations management (pages & API)
- Campaigns module
- Pledges and missed pledges modules
- Pledge payments

## Phase 3: Scorecards & Audit
- Scorecards (zones, departments, overall)
- Audit logs (UI and backend)

## Phase 4: Dashboards & Alerts
- Specialized dashboards for different admin roles (system-admin, member-admin, accountant, executive, pastor)
- Advanced alerts management

## Phase 5: Communications & UI
- Communications compose page
- Topbar and Sidebar UI components (Blade equivalents)
- Advanced RBAC and permission logic (as in src/lib/rbac.ts)

---

**We will implement each phase sequentially.**
