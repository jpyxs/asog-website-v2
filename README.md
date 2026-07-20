<p align="center">
  <img src="./public\assets\img\ASOG TBI\WebP\ASOG-TBI_full-colored_stacked-white.webp" alt="ASOG Technology Business Incubator" width="120">
</p>

<h1 align="center">ASOG TBI Website v2</h1>

<p align="center">
  ASOG TBI website improvements, audit fixes, and enhancements by 2026 DOST-SEI PTP Scholar-Trainees.
</p>

<p align="center">
  <a href="https://asogtbi.com"><img src="https://img.shields.io/badge/Production-asogtbi.com-03558C?style=for-the-badge" alt="Production site"></a>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/CodeIgniter-4.7-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white" alt="CodeIgniter 4.7">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.2-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4.2">
  <img src="https://img.shields.io/badge/License-Proprietary-F2C94C?style=for-the-badge&labelColor=7A4F00&color=F2C94C" alt="Proprietary license">
</p>

## <img src="https://api.iconify.design/lucide:layout-dashboard.svg?color=%2303558C" width="22" height="22" align="absmiddle" alt=""> Overview

The ASOG Technology Business Incubator (ASOG TBI) Website v2 is the official web application for ASOG Technology Business Incubator of Camarines Sur Polytechnic Colleges. It supports the public website, incubatee application workflows, content management, administrative operations, transactional email, public legal pages, and selected engagement features for the ASOG TBI ecosystem.

This version is a fork and modernization of [`DeGrozer/asog-website`](https://github.com/DeGrozer/asog-website). The v2 enhancement period ran from June 15 to July 24, 2026.

## <img src="https://api.iconify.design/lucide:clipboard-check.svg?color=%23B77900" width="22" height="22" align="absmiddle" alt=""> Improvement Basis

Website v2 was guided by the ASOG TBI Website v1 audit conducted in June 2026. The audit identified usability, accessibility, performance, content-management, security, deployment, and maintainability issues in the original implementation. Those findings informed the improvements, fixes, and feature enhancements in this repository.

Audit reference: [ASOG TBI Website Audit Report (June 2026).pdf](<docs/ASOG TBI Website Audit Report (June 2026).pdf>)

## <img src="https://api.iconify.design/lucide:trending-up.svg?color=%2327AE60" width="22" height="22" align="absmiddle" alt=""> v1 to v2 Improvement Summary

The v2 work turned the audited v1 website into a fuller public website, application portal, and admin CMS. It added new management tools, repaired audit findings and workflow bugs, improved day-to-day admin ergonomics, and hardened the site for production use.

- **CMS expansion and content management** - added and refined management areas for posts, hero slides, incubatees, cohorts, organization members, Apply-page FAQs, Lean Canvas templates, public content ordering, and reusable content controls.
- **Superadmin settings and site controls** - added centralized settings for homepage experience, site toggles, application windows, duplicate-email behavior, FAQ visibility, game visibility, intern/member visibility, Lean Canvas templates, Gmail API setup, password management, and other high-impact configuration areas.
- **Admin workflow productivity** - added AJAX search, filtering, sorting, pagination, post previews, shared delete modals, bulk actions, fixed action bars, dirty-state warnings, save-button state handling, scroll restoration, custom admin scripts, improved loading states, and better long-form editing.
- **Applications and applicant review** - improved public application validation, upload handling, duplicate-email behavior, readable uploaded filenames, status messaging, deadline controls, admin review modals, status-update emails, application deletion, private revalidation links, and revalidation-unavailable states.
- **Notifications and operational messaging** - added role-aware and account-targeted admin notifications, per-admin read tracking, notification paging, sidebar/status updates, welcome emails for new admin accounts, contact-message workflow improvements, and clearer operational feedback.
- **Authentication, roles, and account management** - added forgot-password/reset-password flows, password management, Google account linking, admin account modals, role filtering, route-level role enforcement, hardened admin access, and UI fixes around login and account authorization.
- **Email, OAuth, and verification readiness** - moved transactional delivery to Gmail API, added SMTP fallback, added reCAPTCHA support and support text, added legal/app information pages for OAuth review, and improved email templates for application and account workflows.
- **Public website and UX polish** - refined landing content, contact form, footer, news filtering and category behavior, organization display, facilities layout, Services/Homepage CTA treatment, ALTITUDE interactions, incubatee carousel behavior, reduced-motion support, mobile reveal timing, cross-page readability, and the animated ASOG landing loader experience.
- **Performance, media, and assets** - added responsive WebP image handling, compressed team/facility/partner/loader media, local fonts and vendor assets, cache improvements, loader timeline/fallback coordination, lazy-loaded maps, favicon updates, and cleanup of unused repository artifacts.
- **Bug fixes and maintainability** - addressed audit-reported UI, accessibility, upload, application, admin, carousel, map, form, and routing issues; added migrations and seed updates; cleaned records; improved member/photo helpers; tightened upload constraints; modularized admin JavaScript; reorganized documentation; and reduced stale starter-template or one-off development artifacts.

For implementation history, review the repository's [closed pull requests](https://github.com/jpyxs/asog-website-v2/pulls?q=is%3Apr+is%3Aclosed) and [closed issues](https://github.com/jpyxs/asog-website-v2/issues?q=is%3Aissue+is%3Aclosed).

## <img src="https://api.iconify.design/lucide:layers-3.svg?color=%23EB5757" width="22" height="22" align="absmiddle" alt=""> Tech Stack

| Area | Stack | Notes |
| --- | --- | --- |
| Backend | <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP 8.2+"> <img src="https://img.shields.io/badge/CodeIgniter-4.7-EF4223?style=flat-square&logo=codeigniter&logoColor=white" alt="CodeIgniter 4.7"> | CodeIgniter MVC application with controllers, models, filters, libraries, helpers, migrations, and views. |
| Database | <img src="https://img.shields.io/badge/MySQL%20or%20MariaDB-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL or MariaDB"> | Uses CodeIgniter's `MySQLi` driver and migration-based schema management. |
| Frontend | <img src="https://img.shields.io/badge/Tailwind_CSS-4.2-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white" alt="Tailwind CSS 4.2"> | Tailwind source lives in `src/tailwind.css` and compiles to `public/style.css`; public/admin feature CSS and JS live under `public/assets/`. |
| Package management | <img src="https://img.shields.io/badge/Composer-885630?style=flat-square&logo=composer&logoColor=white" alt="Composer"> <img src="https://img.shields.io/badge/npm-CB3837?style=flat-square&logo=npm&logoColor=white" alt="npm"> | Composer manages PHP packages; npm manages Tailwind CLI tooling. |
| Integrations | <img src="https://img.shields.io/badge/Google_API-4285F4?style=flat-square&logo=google&logoColor=white" alt="Google API"> | Gmail API, Google OAuth, Google account linking, Google sign-in paths, and score-based reCAPTCHA. |
| Testing | <img src="https://img.shields.io/badge/PHPUnit-10.5-6C78AF?style=flat-square" alt="PHPUnit 10.5"> | PHPUnit test suite with command-line checks for PHP, JavaScript, and CSS build readiness. |
| Static assets | Local fonts, Font Awesome, Quill, loader vendor files, public images, uploads, favicon assets | Key browser assets are served from `public/assets/`, including `public/assets/vendor/`, `public/assets/loader/`, `public/assets/js/features/`, `public/assets/js/admin/`, and `public/assets/favicon/`. |

## <img src="https://api.iconify.design/lucide:sparkles.svg?color=%232D9CDB" width="22" height="22" align="absmiddle" alt=""> Core Capabilities

- **Public website** - landing page, About, Programs, Services, Facilities, Incubatees, News, Organization, Contact, sitemap, and legal pages.
- **Applications** - public application intake, document uploads, duplicate-email checks, confirmation email, status review, and private revalidation links.
- **Admin CMS** - posts, hero slides, incubatees, cohorts, applications, messages, organization members, FAQs, settings, and admin accounts.
- **Notifications** - role-targeted and account-targeted admin notifications with individual and bulk read state.
- **Authentication and security** - local admin login, password reset, Google OAuth admin login, admin Google account linking, CSRF, role filters, protected routes, reCAPTCHA, and controlled protected-upload serving.
- **Email delivery** - Gmail API as the primary transactional sender with optional SMTP fallback.
- **Game module** - Guess the Startup profile flow, Google sign-in path, play sessions, scoring, abandon flow, and leaderboard APIs.
- **Reviewer support** - privacy policy, terms of service, and app information page for OAuth/Gmail API review.

## <img src="https://api.iconify.design/lucide:folder-tree.svg?color=%239B51E0" width="22" height="22" align="absmiddle" alt=""> Project Structure

```text
asog-website-v2/
|-- app/
|   |-- Config/              Framework, routing, database, email, OAuth, and service config
|   |-- Controllers/         Public pages, admin modules, API endpoints, auth, and deployment actions
|   |-- Database/
|   |   |-- Migrations/      Versioned schema and data migrations
|   |   |-- Seeds/           Seed data for local or controlled setup flows
|   |   `-- MigrationFiles/  Files used by migration-backed defaults
|   |-- Filters/             Auth, role, cache, and request filters
|   |-- Helpers/             View, image, navigation, toast, and domain helpers
|   |-- Libraries/           Mailers, uploads, reCAPTCHA, and Guess the Startup logic
|   |-- Models/              Database models and query helpers
|   `-- Views/               Public pages, admin screens, components, emails, and legal pages
|-- public/
|   |-- assets/
|   |   |-- css/             Admin and feature-specific stylesheets
|   |   |-- favicon/         Browser icons and web app manifest
|   |   |-- games/           Guess the Startup frontend assets
|   |   |-- img/             Brand, partner, facility, team, SDG, and static images
|   |   |-- js/              Public feature scripts and admin dashboard scripts
|   |   |-- loader/          ASOG landing loader assets and vendor files
|   |   `-- vendor/          Committed third-party frontend assets
|   |-- uploads/             Publicly served uploaded media
|   |-- index.php            Web front controller
|   |-- style.css            Compiled Tailwind output
|   `-- robots.txt           Search crawler rules
|-- src/
|   `-- tailwind.css         Tailwind source stylesheet
|-- tests/                   PHPUnit tests and support files
|-- writable/                Runtime cache, logs, sessions, and protected uploads
|-- composer.json            PHP dependencies and scripts
|-- package.json             Frontend build scripts and Tailwind dependency
|-- phpunit.xml.dist         PHPUnit configuration template
`-- spark                    CodeIgniter CLI entrypoint
```

## <img src="https://api.iconify.design/lucide:globe-2.svg?color=%232D9CDB" width="22" height="22" align="absmiddle" alt=""> Public Surface

| Route group | Routes |
| --- | --- |
| Core pages | `/`, `/landing`, `/about`, `/about/logo`, `/programs`, `/services`, `/facilities` |
| Incubatees and applications | `/incubatees`, `/incubatees/cohort-{number}`, `/apply`, `/apply/form`, `/apply/form/thank-you`, `/apply/revalidate/{token}` |
| Content and organization | `/news`, `/news/{slug}`, `/organization`, `/contact`, `/contact/send` |
| Game | `/games/guess-the-startup`, `/games/guess-the-startup/play`, `/games/guess-the-startup/leaderboard`, `/games/guess-the-startup/profile` |
| Legal and discovery | `/privacy-policy`, `/terms-of-service`, `/asog-tbi-website-app`, `/sitemap.xml` |
| Lightweight APIs | `/api/sdgs`, `/api/incubatees`, `/api/games/guess-startup/leaderboard`, `/api/games/guess-startup/start`, `/api/games/guess-startup/submit`, `/api/games/guess-startup/abandon` |
| Controlled uploads | `/uploads/applications/...`, `/uploads/templates/...` |

## <img src="https://api.iconify.design/lucide:panel-top.svg?color=%2327AE60" width="22" height="22" align="absmiddle" alt=""> Functional Areas

### Public Website

- **Landing page** presents the ASOG TBI brand, featured news hero slides, About preview, program highlights, selected incubatees, latest news, organization preview, calls to action, and optional landing-loader experience.
- **About, Programs, Services, and Facilities** explain ASOG TBI's institutional context, partner support, program/service offerings, ALTITUDE 3D experience, and facility media.
- **Incubatees, News, and Organization** expose cohort-based incubatee profiles, SDG display, public news articles, slug history, leadership, staff, mentors, interns, and managed organization sections.
- **Contact and legal pages** provide public contact details, Google Maps location, validated contact form, stored messages, admin notifications, email notification support, privacy policy, terms, and app information pages.

### Applications

- Public users can review application information, submit applicant/startup/team details, upload required files, and complete Lean Canvas/template-related requirements.
- Application settings control the application window, duplicate-email behavior, FAQ visibility, and Lean Canvas template behavior.
- Admin-reviewed applications can receive private revalidation links for controlled resubmission or updates.

### Admin CMS

- Content teams manage posts, hero slides, incubatees, cohorts, organization members, Apply-page FAQs, and public settings.
- Operations users review applications and contact messages, update statuses and remarks, archive or delete records, and send workflow email updates.
- Superadmin users manage administrator accounts, site experience toggles, game/application controls, Gmail API setup, Lean Canvas templates, and high-impact configuration areas.

### Integrations

- Gmail API sends transactional email first, while SMTP can act as a fallback when configured.
- Google OAuth supports admin login, admin account linking, and Guess the Startup player sign-in.
- reCAPTCHA protects public forms, and the legal/reviewer pages support Google OAuth/Gmail API verification.

## <img src="https://api.iconify.design/lucide:file-input.svg?color=%23F2994A" width="22" height="22" align="absmiddle" alt=""> Application Flow

| Step | Behavior |
| --- | --- |
| Intake | Public users review instructions and submit incubatee application details |
| Validation | Form validation, duplicate-email handling, CSRF, and optional reCAPTCHA checks apply |
| Uploads | Applicant files and Lean Canvas/template-related requirements are handled through controlled upload logic |
| Confirmation | Successful submissions route to a thank-you page and can send confirmation email |
| Admin review | Admin users review, update status, add remarks, archive, delete, and trigger applicant status emails |
| Revalidation | Admin-reviewed records can receive private links for controlled resubmission or update |
| Settings | Application window, duplicate-email behavior, FAQ visibility, and Lean Canvas template controls are managed from admin settings |

## <img src="https://api.iconify.design/lucide:monitor-cog.svg?color=%23BD6B2F" width="22" height="22" align="absmiddle" alt=""> Admin Area

Admin login starts at `/asog-admin`. After authentication, the protected dashboard is served under `/admin`.

| Admin module | Responsibilities |
| --- | --- |
| Dashboard | Summaries, sidebar status, and operational entry points |
| Posts | Drafts, publishing, categories, image uploads, previews, slug history, featured posts, and hero slide ordering |
| Incubatees | Company profiles, cohorts, logos, white logos, team members, contacts, SDGs, publishing, and drag/reorder workflows |
| Applications | Review queues, status updates, reviewer remarks, archiving, deletion, bulk actions, and email status updates |
| Messages | Contact-message review, read/unread state, archiving, deletion, and bulk actions |
| Organization | Sectioned member management, mentors, featured entries, modal editing, photo uploads, and ordering |
| FAQs | Apply-page FAQ content, section copy, ordering, publishing, and deletion |
| Settings | Application settings, homepage incubatee filtering, site experience toggles, intern visibility, game availability, password updates, and Lean Canvas template management |
| Accounts | Administrator creation, editing, activation state, roles, welcome email delivery, and Google account authorization/linking |
| Notifications | Role-aware notifications, account-targeted notifications, and mark-read actions |

## <img src="https://api.iconify.design/lucide:shield-check.svg?color=%23BB6BD9" width="22" height="22" align="absmiddle" alt=""> Roles And Permissions

| Role | Access summary |
| --- | --- |
| Public visitor | Browse public content, submit contact messages, access legal pages, and play public game flows when enabled |
| Applicant | Submit applications, upload required files, receive confirmation email, and use private revalidation links |
| Game player | Complete profile/sign-in flow, start play sessions, submit guesses, abandon sessions, and appear on leaderboards when eligible |
| Editor | Content-focused admin access for posts, public incubatee records, cohorts, and Apply-page FAQs |
| Admin | Editor capabilities plus application review, contact-message operations, and organization management |
| Superadmin | Admin capabilities plus site settings, administrator accounts, game/application controls, Lean Canvas template management, Gmail API setup, and other high-impact configuration areas |

## <img src="https://api.iconify.design/lucide:terminal-square.svg?color=%23219653" width="22" height="22" align="absmiddle" alt=""> Local Setup

Install PHP dependencies:

```powershell
composer install
```

Install frontend dependencies:

```powershell
npm install
```

Generate an application key:

```powershell
php spark key:generate
```

Run database migrations:

```powershell
php spark migrate
```

Build the compiled public CSS:

```powershell
npm.cmd run build:css
```

Start the local development server:

```powershell
php spark serve
```

The default CodeIgniter development server is usually available at `http://localhost:8080`.

Before starting, create a local `.env` file and configure at least the app URL, database connection, encryption key, and any integrations needed for the feature being tested.

## <img src="https://api.iconify.design/lucide:settings-2.svg?color=%23828282" width="22" height="22" align="absmiddle" alt=""> Environment Groups

Environment files and production secrets must not be committed. Keep `.env`, `.env.local`, and `.env.production` out of version control.

| Group | Purpose |
| --- | --- |
| `app.*` | Base URL, HTTPS behavior, and framework app settings |
| `database.default.*` | Primary database connection |
| `googleOAuth*` | Google OAuth client, callback, allowed-domain, admin, account-linking, and game sign-in configuration |
| `gmailApi.*` | Gmail API sender, recipient, OAuth client, refresh token, redirect URI, setup mode, and access-token cache settings |
| `smtp.*` | Optional SMTP fallback configuration |
| `recaptcha.*` | Google reCAPTCHA project, keys, score threshold, enforcement mode, and allowed hostnames |
| `emailBrand.*` | Public URLs and branding values used in transactional email templates |
| `deploymentMigrations.*` | Temporary browser-accessible migration runner controls |

Use placeholders in shared examples. Real client IDs, client secrets, refresh tokens, API keys, database passwords, and SMTP passwords belong only in the appropriate local or production environment.

## <img src="https://api.iconify.design/lucide:square-terminal.svg?color=%232F80ED" width="22" height="22" align="absmiddle" alt=""> Common Commands

Build the compiled public CSS:

```powershell
npm.cmd run build:css
```

Watch Tailwind CSS during frontend work:

```powershell
npm.cmd run watch:css
```

Show registered routes:

```powershell
php spark routes
```

Clear the application cache:

```powershell
php spark cache:clear
```

Run the test suite:

```powershell
vendor\bin\phpunit --no-coverage
```

Run Composer's configured test script:

```powershell
composer test
```

Check PHP syntax for one file:

```powershell
php -l path\to\file.php
```

Check JavaScript syntax for one file:

```powershell
node --check path\to\file.js
```

## <img src="https://api.iconify.design/lucide:plug-zap.svg?color=%234285F4" width="22" height="22" align="absmiddle" alt=""> Integrations

| Integration | Notes |
| --- | --- |
| Gmail API | Primary transactional email sender. Requires Google Cloud Gmail API, OAuth web credentials, valid redirect URI, and `gmailApi.refreshToken`. Keep `gmailApi.setupEnabled = false` after setup. |
| SMTP fallback | Optional fallback for transactional mail. Requires `smtp.enabled = true` plus host, user, password, port, crypto, sender email, and sender name. Gmail SMTP should use an app password, not a normal account password. |
| Google OAuth | Used for admin login, admin Google account linking, and Guess the Startup player sign-in. All active callback URLs must be registered in Google Cloud. |
| reCAPTCHA | Score-based Google reCAPTCHA support for public forms. Configure project ID, site key, API key, minimum score, enforcement mode, and allowed hostnames before production enforcement. |

## <img src="https://api.iconify.design/lucide:flask-conical.svg?color=%236C78AF" width="22" height="22" align="absmiddle" alt=""> Testing And QA

Use the checks that match the touched area:

| Changed area | Recommended checks |
| --- | --- |
| PHP files | `php -l path\to\file.php` |
| JavaScript files | `node --check path\to\file.js` |
| Tailwind or compiled CSS | `npm.cmd run build:css` |
| Shared PHP behavior | `vendor\bin\phpunit --no-coverage` |
| Public pages | Browser checks for desktop/mobile layout, forms, media, and navigation |
| Admin workflows | Browser checks for login, role access, forms, uploads, dirty-state behavior, and notifications |
| Email workflows | Confirm transactional email delivery and production-safe links |
| Upload workflows | Confirm public uploads render and protected uploads download through controlled routes |

For production-facing changes, smoke-test the homepage, relevant public route, admin login, touched admin screen, forms, uploads, notifications, email flows, and Guess the Startup if affected.

## <img src="https://api.iconify.design/lucide:rocket.svg?color=%237A4F00" width="22" height="22" align="absmiddle" alt=""> Production Notes

The public production site is [https://asogtbi.com](https://asogtbi.com).

| Requirement | Notes |
| --- | --- |
| Document root | The web server document root should point to `public/`, not the repository root |
| Runtime folders | `writable/` must be writable by the PHP process |
| Upload preservation | Preserve `public/uploads/` and `writable/uploads/` during deployments |
| Environment | Production `.env` values must be configured on the server or deployment environment, never committed |
| CSS build | Rebuild CSS before deployment when Tailwind source, utility usage, or compiled styling changes |
| Database changes | Review migrations, create a backup, and run changes intentionally |
| Smoke testing | Verify public pages, forms, admin login, uploads, notifications, email, and game behavior after changes |

Never run destructive migration commands on production unless a verified backup and rollback plan already exist:

```powershell
php spark migrate:refresh
php spark migrate:rollback
php spark db:seed
```

## <img src="https://api.iconify.design/lucide:lock-keyhole.svg?color=%23D64545" width="22" height="22" align="absmiddle" alt=""> Security And Maintenance

Do not commit credentials, tokens, refresh tokens, API keys, database passwords, SMTP passwords, production environment files, generated logs, sessions, cache files, or local uploads.

| Area | Maintenance expectation |
| --- | --- |
| Secrets | Keep all secrets out of version control and only in the appropriate environment |
| Setup routes | Disable temporary setup or migration routes after use |
| Gmail API | Keep `gmailApi.setupEnabled` disabled after refresh-token setup |
| OAuth | Keep callback URLs and allowed domains aligned with the intended account policy |
| reCAPTCHA | Keep allowed hostnames aligned with the actual served domains |
| Uploads | Preserve `writable/uploads/` and `public/uploads/` during deployment |
| Migrations | Review before production deployment and confirm they preserve existing data |
| Legal and email content | Keep transactional email templates and legal pages aligned with OAuth and privacy review requirements |

## <img src="https://api.iconify.design/lucide:users.svg?color=%2303558C" width="22" height="22" align="absmiddle" alt=""> Contributors

The 2026 DOST-SEI PTP Scholar-Trainees who enhanced this version are listed below. Future maintainers should review [CONTRIBUTING.md](CONTRIBUTING.md) before starting major changes or planning a successor version.

<div align="center">
  <div style="display:inline-block;width:126px;text-align:center;vertical-align:top;margin:0 6px 16px;">
    <a href="https://github.com/ferenimedez-stab"><img src="https://wsrv.nl/?url=github.com/ferenimedez-stab.png%3Fsize%3D160&w=72&h=72&fit=cover&mask=circle" width="72" height="72" alt="ferenimedez-stab"><br><sub><b>ferenimedez-stab</b></sub></a>
  </div>
  <div style="display:inline-block;width:126px;text-align:center;vertical-align:top;margin:0 6px 16px;">
    <a href="https://github.com/jazz-lnz"><img src="https://wsrv.nl/?url=github.com/jazz-lnz.png%3Fsize%3D160&w=72&h=72&fit=cover&mask=circle" width="72" height="72" alt="jazz-lnz"><br><sub><b>jazz-lnz</b></sub></a>
  </div>
  <div style="display:inline-block;width:126px;text-align:center;vertical-align:top;margin:0 6px 16px;">
    <a href="https://github.com/johncarlonas"><img src="https://wsrv.nl/?url=github.com/johncarlonas.png%3Fsize%3D160&w=72&h=72&fit=cover&mask=circle" width="72" height="72" alt="johncarlonas"><br><sub><b>johncarlonas</b></sub></a>
  </div>
  <div style="display:inline-block;width:126px;text-align:center;vertical-align:top;margin:0 6px 16px;">
    <a href="https://github.com/Arrvsssogood"><img src="https://wsrv.nl/?url=github.com/Arrvsssogood.png%3Fsize%3D160&w=72&h=72&fit=cover&mask=circle" width="72" height="72" alt="Arrvsssogood"><br><sub><b>Arrvsssogood</b></sub></a>
  </div>
  <div style="display:inline-block;width:126px;text-align:center;vertical-align:top;margin:0 6px 16px;">
    <a href="https://github.com/mprestado"><img src="https://wsrv.nl/?url=github.com/mprestado.png%3Fsize%3D160&w=72&h=72&fit=cover&mask=circle" width="72" height="72" alt="mprestado"><br><sub><b>mprestado</b></sub></a>
  </div>
  <div style="display:inline-block;width:126px;text-align:center;vertical-align:top;margin:0 6px 16px;">
    <a href="https://github.com/jpyxs"><img src="https://wsrv.nl/?url=github.com/jpyxs.png%3Fsize%3D160&w=72&h=72&fit=cover&mask=circle" width="72" height="72" alt="jpyxs"><br><sub><b>jpyxs</b></sub></a>
  </div>
</div>

## <img src="https://api.iconify.design/lucide:scroll-text.svg?color=%23B77900" width="22" height="22" align="absmiddle" alt=""> License

This project is proprietary software developed for ASOG Technology Business Incubator. All rights are reserved unless written permission is granted by ASOG Technology Business Incubator.

See the full license notice in [LICENSE](LICENSE).

Third-party open-source components, including CodeIgniter and other dependencies, remain governed by their respective licenses.

For permission requests, reuse questions, or licensing concerns, contact ASOG TBI at [asogtbi@cspc.edu.ph](mailto:asogtbi@cspc.edu.ph).
