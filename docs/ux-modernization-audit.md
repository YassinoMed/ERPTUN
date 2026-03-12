# UX Modernization Audit

## Phase 1 - Audit

### Current UI architecture
- Main admin shell: `resources/views/layouts/admin.blade.php`
- Header: `resources/views/partials/admin/header.blade.php`
- Sidebar: `resources/views/partials/admin/menu.blade.php`
- Footer scripts: `resources/views/partials/admin/footer.blade.php`
- Shared frontend enhancements: `public/css/ux-enhancements.css`, `public/js/ux-enhancements.js`
- Business dashboards: `resources/views/dashboard/*`
- Heavy list patterns: `resources/views/*/index.blade.php`

### Existing strengths
- Shared admin shell already centralizes menu, header and footer.
- Simple Datatables is already used across list pages.
- A first UX layer already exists for search, notifications, recents, favorites and dark mode.
- The application is strongly Blade-driven, which makes progressive enhancement safer than a full frontend rewrite.

### Main UX issues observed
- Visual hierarchy is inconsistent between modules.
- Header actions are functional but not descriptive enough.
- Sidebar density is high and scanning cost is high on large module catalogs.
- List pages mostly stop at a raw table in a generic card.
- Dashboard widgets are visually inconsistent across domains.
- Forms rely on business correctness, but affordance and feedback are uneven.
- Mobile behavior exists, but not at a premium ERP level yet.

## Phase 2 - Improvement matrix

### Quick wins
- Stronger page header hierarchy.
- Better command/search affordance.
- Smarter list/table presentation.
- Generic form feedback and autosave hooks.
- Cleaner KPI cards for module index screens.

### Structural improvements
- Sidebar compact mode with persistence.
- Reusable list and form surfaces.
- Unified design tokens for cards, surfaces, status chips and shadows.
- Better dashboard shell for all domains.

### Major refactors
- Per-domain dashboards.
- Drawer-based edit flows for heavy modules.
- Advanced mobile POS and warehouse flows.
- Saved views and persistent filters per module.

### Premium improvements
- Personalized home by role.
- AI summaries and anomaly hints.
- Visual workflow builder surfaces.
- Unified notification and activity center refinements.

## Phase 3 - Roadmap

### Wave 1
- Admin shell modernization
- Sidebar compact mode
- Header command launcher
- Generic list and form surfaces
- Dashboard card polish

### Wave 2
- CRM, customers, deliveries, invoices
- Purchases and supplier flows
- Stock and warehouse screens
- Medical workspace screens

### Wave 3
- POS touchscreen refinement
- Production operator screens
- Agro traceability screens
- Governance and document workspace

### Wave 4
- Saved views
- Role personalization
- Mobile advanced flows
- AI assistant overlays

## Phase 4 - Design system

### Core principles
- Stronger visual hierarchy
- Faster scanability
- Fewer hard edges, more layered surfaces
- High-density data with calmer chrome
- Mobile-safe actions and spacing

### Tokens
- Surface radius: 18px primary, 14px secondary
- Soft shadows for cards, stronger shadows only for overlays
- Status chips with semantic tint backgrounds
- Spacious page header with module pill, subtitle and action rail
- Reusable KPI cards for module index screens

### Shared component targets
- Page header
- Command launcher
- Sidebar workspace card
- KPI cards
- List shell
- Form shell
- Data table search and pagination skin
- Empty and loading states
