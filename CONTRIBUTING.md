# Contributing And Future Maintenance

This guide is for future ASOG TBI maintainers, scholar-trainees, and authorized contributors who may continue improving the ASOG TBI Website after version 2.

## Repository Direction

ASOG TBI Website v2 is a proprietary application developed for ASOG Technology Business Incubator. Future major redevelopment work should be handled as a new versioned fork or successor repository, such as an ASOG TBI Website v3 effort, instead of rewriting the production v2 history directly.

Recommended direction for a future major version:

```text
Fork this repository
Review the v2 audit-driven changes
Create a new version branch or successor repository
Plan v3 scope with ASOG TBI stakeholders
Preserve production data, uploads, and integration requirements
Document migration paths before replacing any live workflow
```

## Before Making Changes

Start from the current codebase, not assumptions from the original v1 repository.

Confirm the affected area before editing:

```powershell
php spark routes
```

Install or refresh dependencies when needed:

```powershell
composer install
npm install
```

Build CSS when Tailwind utilities, Tailwind source, or compiled frontend styling changes:

```powershell
npm.cmd run build:css
```

## Development Expectations

Keep changes focused and traceable.

- Prefer existing CodeIgniter patterns, helpers, models, filters, and view structures.
- Keep public website, admin CMS, application flow, email delivery, and game logic separated by their existing boundaries.
- Preserve uploads, migration history, admin role behavior, and production-facing URLs.
- Use migrations for schema changes instead of manual database edits.
- Keep transactional email, OAuth, reCAPTCHA, and upload changes carefully tested because they affect real users and external integrations.
- Avoid committing local environment files, generated logs, cache files, sessions, test exports, or real credentials.

## Suggested Workflow

Use a dedicated branch for each feature, fix, or cleanup:

```powershell
git checkout -b feature/descriptive-change-name
```

Make the smallest complete change that solves the issue. Before handoff, run the checks that match the touched files:

```powershell
php -l path\to\file.php
node --check path\to\file.js
npm.cmd run build:css
vendor\bin\phpunit --no-coverage
```

Use pull requests for review. Summaries should explain what changed, why it changed, and how it was tested.

## Areas That Need Extra Care

### Applications

The application flow includes public submission, duplicate-email behavior, file uploads, status updates, applicant email notifications, and private revalidation links. Changes should be tested from both public applicant and admin reviewer perspectives.

### Admin CMS

Admin screens include role-aware access, AJAX workflows, dirty-state behavior, notifications, uploads, and long-form editing. Confirm that editor, admin, and superadmin access still matches the intended role boundaries.

### Email And OAuth

Gmail API, SMTP fallback, Google OAuth, Google account linking, and reCAPTCHA depend on environment configuration. Never commit real client IDs, secrets, refresh tokens, SMTP passwords, or API keys.

### Public Pages

Public changes should be checked on desktop and mobile viewports. Pay attention to image delivery, accessibility labels, reduced-motion behavior, forms, navigation, footer content, and SEO-facing routes such as the sitemap and legal pages.

## Future Version Planning

For a future v3 effort, document these before implementation starts:

- Current production routes and workflows that must remain compatible.
- Database tables, migrations, uploads, and records that must be preserved.
- Admin roles and permissions that must carry forward.
- Email, OAuth, reCAPTCHA, and public legal-page requirements.
- Known v2 limitations, deferred improvements, and stakeholder-approved scope.
- A rollback or migration plan for any production replacement.

## Ownership And License

This project is proprietary to ASOG Technology Business Incubator. See [LICENSE](LICENSE) before reusing, distributing, or adapting the code outside authorized ASOG TBI development work.
