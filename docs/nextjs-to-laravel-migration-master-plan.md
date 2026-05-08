# Next.js to Laravel Migration Master Plan

## Objective

Rebuild the full business process currently implemented in `BCC_CMS_PROJECT` inside `bcc-cms` using Laravel full-stack, while allowing a new Laravel-specific layout and branding.

This plan treats **business-process parity** as mandatory and **UI parity** as optional.

## Verified Source Baseline

- Source application: Next.js 15 App Router + TypeScript + Prisma + MySQL
- Target application: Laravel 12 + Blade + Eloquent + MySQL
- Verified source scope:
  - 85 page screens
  - 71 API route handlers
  - 36 Prisma domain models
- Authentication style in source:
  - cookie-based login
  - RBAC enforced in middleware and route logic
  - database-backed system users
- Data store:
  - MySQL remains the system of record
- Migration constraint:
  - exact process replication
  - no requirement to preserve the old branding/layout
  - execute module-by-module, not big-bang

## Non-Negotiable Migration Rules

1. Do not change business rules while migrating unless the current behavior is clearly broken and the change is approved.
2. Every Laravel module must be tested against the current Next.js flow before sign-off.
3. Shared data definitions must be established first so later modules do not drift.
4. Each phase must finish migrations, Eloquent models, controllers, validation, views, permissions, and tests before moving on.
5. No module is considered done until list, create, detail, edit/update, delete/archive behavior, filters, and role access are covered where applicable.

## Target Laravel Architecture

Use this structure consistently throughout the migration:

- `app/Models` for Eloquent models
- `app/Http/Controllers` for web and API controllers
- `app/Http/Requests` for request validation
- `app/Policies` or permission middleware for access control
- `app/Services` for cross-module business logic
- `app/Support` or `app/Domain` for reusable query/report helpers if needed
- `resources/views` for Blade templates
- `resources/views/layouts` for app shell
- `resources/views/components` for shared Blade components
- `routes/web.php` for Blade workflows
- `routes/api.php` only if a true API is needed during migration; otherwise prefer web routes and controllers unless integration requires JSON endpoints
- `database/migrations` for schema
- `database/seeders` for roles, permissions, admin users, lookup data
- `tests/Feature` for workflow and HTTP coverage
- `tests/Unit` for calculation and helper logic

## Cross-Cutting Foundation Requirements

These apply to every phase.

### Security and Access

- Laravel authentication for system users
- RBAC equivalent to the Next.js permission model
- route-level and action-level authorization
- active/inactive user status enforcement
- last login tracking

### Data and Auditing

- preserve MySQL as primary database
- map Prisma tables to Laravel migrations and Eloquent models
- retain audit logging for create, update, delete, approval, and send actions
- standardize timestamps and foreign keys
- preserve current status values and lifecycle transitions

### Shared UI and Workflow Standards

- admin layout shell
- sidebar navigation by permission
- topbar/user menu
- reusable table/search/pagination patterns
- reusable status badges and summary cards
- reusable member, visitor, department, and zone selectors
- export-ready screens for leadership-facing lists

### Shared Technical Standards

- use Form Requests for validation
- centralize file upload handling
- centralize pagination/filter helpers
- centralize response flash patterns for Blade flows
- centralize audit logging
- centralize permission checks
- centralize report aggregation logic

## Domain Inventory to Preserve

### Source Models

- Member
- Visitor
- Campaign
- Pledge
- PledgePayment
- Donation
- IncomeType
- OtherIncome
- Expenditure
- Employee
- Payroll
- PayrollCategory
- DepartmentIncome
- DepartmentExpense
- Family
- Payment
- SystemUser
- AuditLog
- FollowUpTask
- FollowUpHistory
- Service
- AttendanceRecord
- PastoralCase
- PastoralCaseNote
- PrayerRequest
- Alert
- MemberTimelineEvent
- Department
- DepartmentMember
- Zone
- ZoneMember
- Event
- EventRegistration
- VolunteerAssignment
- Communication
- CommunicationDelivery

### Source Major Modules

- Authentication and user management
- Role-based dashboards
- Members
- Families
- Donations
- Campaigns
- Pledges
- Pledge payments
- Legacy payments
- Other income and income types
- Department income and department expenses
- Expenditures
- Employees and payroll
- Reports
- Audit logs
- Visitors
- Follow-up pipeline and tasks
- Services and attendance
- Pastoral care and case notes
- Prayer requests
- Alerts
- Member timeline
- Departments
- Zones
- Communications
- Events
- Volunteers
- Scorecards
- Executive dashboard

## Master Execution Order

The migration should run in six phases.

1. Phase 0: Platform Foundation
2. Phase 1: Identity, Access, and Core Records
3. Phase 2: Financial Operations
4. Phase 3: Ministry Care and Retention
5. Phase 4: Governance, Events, and Leadership Operations
6. Phase 5: Reporting, Analytics, Hardening, and Cutover

This order is chosen to reduce rework:

- Auth and permissions must exist before restricted modules
- Members and families must exist before ministry modules
- Finance depends on members, campaigns, and shared uploads
- Ministry intelligence depends on members, families, services, and users
- Scorecards and executive dashboards depend on data from earlier phases

## Phase 0: Platform Foundation

### Goal

Establish the Laravel base architecture that every later module will depend on.

### Deliverables

- application layout shell
- authenticated app area
- sidebar navigation skeleton
- dashboard landing route placeholder
- login/logout flow
- seeded initial admin user
- roles and permissions tables or equivalent permission seed strategy
- middleware/policy layer for permission enforcement
- audit logging service
- file upload service for attachments
- shared table, search, filter, flash-message, and pagination Blade components
- date and currency formatting helpers
- common status badge component
- common empty state and confirm dialog patterns

### Database Work

- users/system users equivalent table
- roles table
- permissions table
- role_permission pivot
- user_role pivot or direct role key if you intentionally keep single-role users
- audit_logs table

### Business Rules to Preserve

- inactive users cannot log in
- successful login updates `last_login_at`
- role checks gate both views and data actions
- audit logs capture actor, entity, action, before, after, IP, and timestamp

### Laravel Build Checklist

- auth controllers and views
- role/permission seeder
- auth middleware
- policy or permission middleware helper
- layout templates
- base dashboard route
- audit logger service class
- upload storage convention
- shared Blade components

### Exit Criteria

- admin can log in and out
- inactive user is blocked
- permission middleware works
- audit log entries are stored
- shared layout is ready for modules

## Phase 1: Identity, Access, and Core Records

### Goal

Replicate all people and base administration processes that other modules depend on.

### Modules

- dashboards
- users management
- members
- families
- departments foundation
- zones foundation

### Screens to Build

- `/`
- `/login`
- `/dashboard`
- `/dashboard/system-admin`
- `/dashboard/system-admin/users`
- `/dashboard/pastor`
- `/dashboard/accountant`
- `/dashboard/member-admin`
- `/members`
- `/members/add`
- `/members/{id}`
- `/members/{id}/edit`
- `/families`
- `/families/add`
- `/families/{id}`
- `/families/{id}/edit`
- `/departments`
- `/departments/add`
- `/departments/{id}`
- `/departments/{id}/members`
- `/zones`
- `/zones/add`
- `/zones/{id}`

### Backend Behaviors to Replicate

#### Users and Access

- create system user
- update system user
- activate/deactivate user
- track last login
- enforce role-based route visibility
- keep role dashboards separated by responsibility

#### Members

- list with search and pagination
- dropdown mode with `all=true` equivalent behavior
- create member
- view member detail
- edit member
- export members
- import members
- member timeline endpoint placeholder ready for Phase 3 population

#### Families

- list with search and pagination
- create family
- detail view
- edit family
- delete family if current source allows it
- export families
- import families

#### Departments and Zones Foundation

- create department
- list departments
- view department detail
- assign and view department members
- create zone
- list zones
- view zone detail
- assign and view zone members

### Key Data Tables

- `system_users` or `users`
- `members`
- `families`
- `departments`
- `department_members`
- `zones`
- `zone_members`
- `audit_logs`

### Parity Checks

- member create/edit fields match source data structure
- family records preserve same fields and relationships
- role access matches current source permissions
- department and zone membership can later feed attendance, communications, and scorecards

### Exit Criteria

- users, members, families, departments, and zones are working end-to-end
- permissions are enforced in UI and server logic
- imports/exports are accounted for
- dashboard redirects by role work

## Phase 2: Financial Operations

### Goal

Replicate all finance-related operational flows and their reporting dependencies.

### Modules

- donations
- campaigns
- pledges
- pledge payments
- payments legacy flow
- other income
- income types
- income records summary
- department income
- department expenses
- expenditures
- payroll
- payroll categories
- employees
- missed pledges

### Screens to Build

- `/donations`
- `/donations/add`
- `/donations/records`
- `/donations/records/{id}`
- `/campaigns`
- `/campaigns/new`
- `/pledges/add`
- `/pledges/records`
- `/pledge-payments`
- `/pledge-payments/new`
- `/missed-pledges`
- `/add-income`
- `/income-types`
- `/income-records`
- `/income-records/{id}`
- `/department-income`
- `/department-income/new`
- `/department-expenses`
- `/department-expenses/new`
- `/expenditures`
- `/expenditures/new`
- `/payroll/add`
- `/payroll/records`
- `/payroll/categories`

### Backend Behaviors to Replicate

#### Donations

- add donation linked to member
- list donation records
- view donation detail
- fetch donations by member
- preserve payment method, amount, date, and attachment handling

#### Campaigns

- create campaign
- list campaigns
- preserve amount required, start date, final date, progress status, and status

#### Pledges and Pledge Payments

- create pledge for registered or unregistered person
- list pledges
- compute paid and due amounts
- create pledge payment
- list pledge payments
- preserve campaign linkage
- preserve due date logic used by missed pledges

#### Legacy Payments

- preserve `payments` flow if it is still used anywhere in source logic
- confirm whether it remains active or becomes internal-only during migration

#### Other Income and Income Types

- manage income types
- create other income records
- allow registered or unregistered contributor data
- support attachments
- support grouped income summary used by `/income-records`

#### Department Income and Department Expenses

- create department income entry
- create department expense entry
- list with search/filter/pagination if present in current flow

#### Expenditures

- create expenditure with attachment
- list expenditures
- preserve category, payment method, reference, comment, and status

#### Payroll

- manage employees
- manage payroll categories
- create payroll record
- preserve salary calculation fields:
  - salary
  - tax percent
  - church staffs addition
  - PAYE
  - other amount
  - net salary
  - take home
  - paid amount

#### Missed Pledges

- reproduce current behavior exactly:
  - source reads pledges
  - filters pledges where paid is less than pledge amount
  - due date earlier than today

### Key Data Tables

- `donations`
- `campaigns`
- `pledges`
- `pledge_payments`
- `payments`
- `income_types`
- `other_income`
- `department_income`
- `department_expenses`
- `expenditures`
- `employees`
- `payrolls`
- `payroll_categories`

### Parity Checks

- finance totals match Next.js outputs for the same dataset
- attachments save and render correctly
- pledge due and paid calculations match source behavior
- grouped income records and missed pledges views match source output

### Exit Criteria

- all finance modules are operational in Laravel
- reports can safely be built on Laravel finance data
- file uploads work consistently across finance modules

## Phase 3: Ministry Care and Retention

### Goal

Replicate the ministry intelligence workflows that handle visitor retention, attendance, pastoral intervention, prayer tracking, alerts, and member lifecycle history.

### Modules

- visitors
- follow-up pipeline
- follow-up tasks
- follow-up history
- services
- attendance
- attendance reports
- pastoral care
- pastoral case notes
- prayer requests
- alerts
- member timeline

### Screens to Build

- `/visitors`
- `/visitors/add`
- `/visitors/{id}`
- `/visitors/{id}/edit`
- `/follow-up`
- `/follow-up/pipeline`
- `/follow-up/tasks`
- `/follow-up/tasks/{id}`
- `/attendance`
- `/attendance/record`
- `/attendance/services`
- `/attendance/services/add`
- `/attendance/reports`
- `/pastoral-care`
- `/pastoral-care/add`
- `/pastoral-care/{id}`
- `/pastoral-care/{id}/notes`
- `/prayer-requests`
- `/prayer-requests/{id}`
- `/alerts`

### Backend Behaviors to Replicate

#### Visitors

- create visitor
- search visitors
- update visitor profile
- update visitor status/stage
- convert visitor to member
- preserve converted-member linkage

Suggested visitor statuses to preserve:

- `new`
- `contacted`
- `counseled`
- `joined_zone`
- `in_class`
- `converted`

#### Follow-Up

- create task for member, visitor, or family
- assign task to system user
- track task type, priority, due date, and status
- update task status
- record follow-up history entries
- expose pipeline/board view
- expose overdue task logic

Suggested task types to preserve:

- `call`
- `sms`
- `visit`
- `prayer`
- `counseling`
- `zone_assignment`

#### Services and Attendance

- create service/session
- record attendance for member, visitor, or family
- preserve zone and department context if source uses them
- generate attendance reports by date/service/member/zone/department
- support inactivity detection

Suggested attendance statuses to preserve:

- `present`
- `absent`
- `excused`

#### Pastoral Care

- create case
- assign case
- add notes
- close case
- filter by urgency and status
- preserve note visibility where source suggests restriction

Suggested pastoral case types to preserve:

- `counseling`
- `hospital_visit`
- `bereavement`
- `discipleship`
- `family_support`
- `prayer_support`

#### Prayer Requests

- create prayer request for member or visitor
- update visibility and status
- assign request where current source supports assignment

#### Alerts

- list alerts
- acknowledge alert
- resolve alert
- run alert-generation logic
- preserve inactive-member alert behavior
- later extend with attendance and pledge alert rules already implied by source docs

Suggested alert types to preserve:

- `inactive_member`
- `missed_pledge`
- `birthday`
- `anniversary`
- `visitor_followup`
- `pastoral_case_due`

#### Member Timeline

- aggregate membership milestones
- aggregate giving activity
- aggregate attendance activity
- aggregate follow-up and pastoral events
- render unified member history

### Key Data Tables

- `visitors`
- `follow_up_tasks`
- `follow_up_history`
- `services`
- `attendance_records`
- `pastoral_cases`
- `pastoral_case_notes`
- `prayer_requests`
- `alerts`
- `member_timeline_events`

### Parity Checks

- visitor conversion creates equivalent member data
- attendance and follow-up data can feed alerts
- pastoral note visibility matches source intent
- member timeline merges events consistently

### Exit Criteria

- visitor retention and pastoral workflows work fully in Laravel
- attendance recording and reporting are accurate
- alerts and member timeline are populated from real activity

## Phase 4: Governance, Events, and Leadership Operations

### Goal

Replicate leadership coordination features for departments, zones, communications, events, volunteers, scorecards, and executive oversight.

### Modules

- departments full workflows
- zones full workflows
- communications
- events
- volunteers
- scorecards
- executive dashboard

### Screens to Build

- `/communications`
- `/communications/compose`
- `/communications/{id}`
- `/events`
- `/events/add`
- `/events/{id}`
- `/events/{id}/assignments`
- `/volunteers`
- `/volunteers/assignments/add`
- `/scorecards`
- `/scorecards/zones`
- `/scorecards/departments`
- `/dashboard/executive`

### Backend Behaviors to Replicate

#### Communications

- create draft communication
- save channel, audience type, subject, message, and filters
- send to filtered audience
- create delivery records per recipient
- store delivery status and provider response
- preserve communication history

Suggested channels to preserve:

- `sms`
- `email`
- `whatsapp`
- `internal`

Suggested audience types confirmed in source docs:

- `all_members`
- `all_visitors`
- `everyone`

#### Events

- create event
- list and view events
- register members or visitors
- assign volunteers to event roles
- preserve event status lifecycle and date ranges

#### Volunteers

- create volunteer assignment
- link assignment to member, event, and optionally department
- preserve assignment role, report time, status, and notes

#### Departments and Zones Analytics Role

- ensure departments and zones can be filtered across attendance, communications, and reporting
- ensure leadership views can derive active membership counts

#### Scorecards

- build zone scorecards
- build department scorecards
- preserve read-only analytics behavior from source
- preserve ranking and KPI aggregation logic

Source KPIs documented in Next.js project:

- zone member count
- zone attendance
- zone follow-up completion percentage
- department member count
- completed volunteer assignments
- department attendance

#### Executive Dashboard

- aggregate leadership KPIs
- preserve sections documented in source:
  - membership
  - giving
  - pastoral
  - ministry coordination

### Key Data Tables

- `communications`
- `communication_deliveries`
- `events`
- `event_registrations`
- `volunteer_assignments`
- reused `departments`, `department_members`, `zones`, `zone_members`

### Parity Checks

- communication send flow creates delivery rows
- event registrations and volunteer assignments match source relationships
- scorecards compute from live module data, not hardcoded values
- executive dashboard numbers match Laravel source-of-truth data

### Exit Criteria

- leadership coordination modules are fully usable in Laravel
- communications, events, and volunteer flows are stable
- scorecards and executive dashboard are trustworthy

## Phase 5: Reporting, Analytics, Hardening, and Cutover

### Goal

Finalize reporting parity, harden the system, and prepare safe operational cutover from Next.js to Laravel.

### Modules

- reports index
- reports members
- reports finance
- reports pledges
- audit logs review UX
- final dashboard KPI parity
- regression coverage across all modules

### Screens to Build or Finalize

- `/reports`
- `/reports/members`
- `/reports/finance`
- `/reports/pledges`
- `/audit-logs`

### Backend Behaviors to Finalize

- members report aggregation
- finance report aggregation
- pledge report aggregation
- CSV/print/export parity for leadership-facing reports
- audit log filtering and pagination
- dashboard KPI validation against migrated data

### Hardening Checklist

- permission regression tests
- form validation regression tests
- file upload tests
- export tests
- dashboard/report calculation tests
- null/empty data behavior tests
- activity logging tests
- seeders for stable demo/admin setup
- backup and rollback procedure

### Cutover Checklist

1. Freeze schema changes in the Next.js app before final sync.
2. Confirm Laravel schema and lookup data match production MySQL.
3. Verify all modules against parity checklist.
4. Run user acceptance pass module-by-module.
5. Migrate or sync remaining delta data.
6. Point users to Laravel app.
7. Keep Next.js app available briefly for rollback-only reference.

### Exit Criteria

- reports are trusted by leadership
- audits are reviewable
- all major module regressions are covered
- cutover can happen with controlled rollback

## Source Screen Inventory

This inventory was verified from the Next.js project and should be treated as the parity checklist for UI workflows.

### Public and Auth

- `/`
- `/login`

### Dashboards

- `/dashboard`
- `/dashboard/accountant`
- `/dashboard/executive`
- `/dashboard/member-admin`
- `/dashboard/pastor`
- `/dashboard/system-admin`
- `/dashboard/system-admin/users`

### Core Records

- `/members`
- `/members/add`
- `/members/{id}`
- `/members/{id}/edit`
- `/families`
- `/families/add`
- `/families/{id}`
- `/families/{id}/edit`
- `/departments`
- `/departments/add`
- `/departments/{id}`
- `/departments/{id}/members`
- `/zones`
- `/zones/add`
- `/zones/{id}`

### Finance

- `/donations`
- `/donations/add`
- `/donations/records`
- `/donations/records/{id}`
- `/campaigns`
- `/campaigns/new`
- `/pledges/add`
- `/pledges/records`
- `/pledge-payments`
- `/pledge-payments/new`
- `/missed-pledges`
- `/add-income`
- `/income-types`
- `/income-records`
- `/income-records/{id}`
- `/department-income`
- `/department-income/new`
- `/department-expenses`
- `/department-expenses/new`
- `/expenditures`
- `/expenditures/new`
- `/payroll/add`
- `/payroll/records`
- `/payroll/categories`

### Ministry Care

- `/visitors`
- `/visitors/add`
- `/visitors/{id}`
- `/visitors/{id}/edit`
- `/follow-up`
- `/follow-up/pipeline`
- `/follow-up/tasks`
- `/follow-up/tasks/{id}`
- `/attendance`
- `/attendance/record`
- `/attendance/reports`
- `/attendance/services`
- `/attendance/services/add`
- `/pastoral-care`
- `/pastoral-care/add`
- `/pastoral-care/{id}`
- `/pastoral-care/{id}/notes`
- `/prayer-requests`
- `/prayer-requests/{id}`
- `/alerts`

### Leadership Operations

- `/communications`
- `/communications/compose`
- `/communications/{id}`
- `/events`
- `/events/add`
- `/events/{id}`
- `/events/{id}/assignments`
- `/volunteers`
- `/volunteers/assignments/add`
- `/scorecards`
- `/scorecards/zones`
- `/scorecards/departments`
- `/reports`
- `/reports/members`
- `/reports/finance`
- `/reports/pledges`
- `/audit-logs`

## Source Backend Inventory

This inventory was verified from the Next.js project and should be treated as the parity checklist for backend endpoints and server behaviors.

### Auth and Access

- `/api/auth/login`
- `/api/auth/register`
- `/api/users`
- `/api/users/{id}`
- `/api/audit-logs`

### Dashboard and Reports

- `/api/dashboard`
- `/api/dashboard/executive`
- `/api/reports/members`
- `/api/reports/finance`
- `/api/reports/pledges`

### Members, Families, Departments, Zones

- `/api/members`
- `/api/members/export`
- `/api/members/import`
- `/api/members/{id}`
- `/api/members/{id}/timeline`
- `/api/families`
- `/api/families/export`
- `/api/families/import`
- `/api/families/{id}`
- `/api/departments`
- `/api/departments/{id}`
- `/api/departments/{id}/members`
- `/api/zones`
- `/api/zones/{id}`
- `/api/zones/{id}/members`

### Finance

- `/api/donations`
- `/api/donations/member/{id}`
- `/api/campaigns`
- `/api/pledges`
- `/api/pledge-payments`
- `/api/payments`
- `/api/income-types`
- `/api/income-types/{id}`
- `/api/other-income`
- `/api/other-income/{id}`
- `/api/department-income`
- `/api/department-expenses`
- `/api/expenditures`
- `/api/employees`
- `/api/payroll`
- `/api/payroll-categories`

### Ministry Care

- `/api/visitors`
- `/api/visitors/{id}`
- `/api/follow-up/tasks`
- `/api/follow-up/tasks/{id}`
- `/api/follow-up/history`
- `/api/follow-up/pipeline`
- `/api/services`
- `/api/services/{id}`
- `/api/attendance`
- `/api/attendance/{id}`
- `/api/attendance/reports`
- `/api/pastoral-care`
- `/api/pastoral-care/{id}`
- `/api/pastoral-care/{id}/notes`
- `/api/prayer-requests`
- `/api/prayer-requests/{id}`
- `/api/alerts`
- `/api/alerts/{id}`
- `/api/alerts/run`

### Leadership Operations

- `/api/communications`
- `/api/communications/{id}`
- `/api/communications/{id}/send`
- `/api/events`
- `/api/events/{id}`
- `/api/events/{id}/registrations`
- `/api/events/{id}/assignments`
- `/api/volunteers/assignments`
- `/api/volunteers/assignments/{id}`
- `/api/scorecards/zones`
- `/api/scorecards/departments`

## Recommended First Build Sequence Inside Phase 0 and Phase 1

When implementation starts, use this exact order:

1. auth tables, seeders, login flow
2. permission middleware and audit logging
3. app layout, sidebar, dashboard landing
4. users management
5. members
6. families
7. departments
8. zones

After that, start Phase 2 financial modules.

## Definition of Done for Every Module

A module is only complete when all items below are true.

1. Database schema exists and is migrated.
2. Eloquent model relationships are correct.
3. Validation rules are implemented.
4. Authorization rules are implemented.
5. List, create, detail, update, and delete/archive behavior is covered where applicable.
6. Filters, pagination, and attachments work where the source supports them.
7. Audit logging is in place for meaningful actions.
8. Blade screens are usable.
9. Feature tests exist for the primary workflow.
10. Output was checked against the current Next.js behavior.

## Immediate Next Step

Start with **Phase 0** and do not begin any business module before auth, permissions, audit logging, shared layout, and core infrastructure are ready.
