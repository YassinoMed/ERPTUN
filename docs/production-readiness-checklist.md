# Production Readiness Checklist

This checklist consolidates the final rollout controls for the ERP core and the recently added vertical modules.

## Core

- Warm menu, permissions and settings caches after deployments.
- Validate API clients, secret rotation and API logs.
- Review pending approvals and failed automation runs.
- Confirm saved reports and scheduled exports still execute.
- Confirm onboarding, plan requests and addon lifecycle screens load correctly.

## Security

- Review active sessions and revoke stale sessions.
- Validate 2FA status for privileged users.
- Confirm IP restrictions and access scopes are aligned with branch, warehouse, department and service ownership.
- Review sensitive access logs for medical, customer, invoice and document flows.

## Business Domains

- Validate executive dashboard widgets against source lists.
- Validate medical advanced routes and patient portal access.
- Validate retail operations, customer portal and supplier portal.
- Validate agri operations, FEFO and traceability screens.
- Validate industrial planning, realtime board and BI analytics.

## Release Controls

- Run targeted feature tests for changed phases.
- Rebuild Blade cache.
- Recheck route registration for newly added screens.
- Review plans and permissions after seeding.
- Deploy progressively and validate tenant by tenant where possible.
