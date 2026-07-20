<p align="center">
  <img src="./public\assets\img\ASOG TBI\WebP\ASOG-TBI_full-colored_stacked-white.webp" alt="ASOG Technology Business Incubator" width="120">
</p>

<h1 align="center">ASOG TBI Website v2</h1>

<p align="center">
  ASOG TBI website improvements, audit fixes, and enhancements by 2026 DOST-SEI PTP Scholar-Trainees.
</p>

## Overview

The ASOG Technology Business Incubator (ASOG TBI) Website v2 is the official web application for the ASOG Technology Business Incubator of Camarines Sur Polytechnic Colleges. It provides the public website, incubatee application workflows, content management tools, administrative operations, and selected engagement features for the ASOG TBI ecosystem.

This version is a fork and modernization of [`DeGrozer/asog-website`](https://github.com/DeGrozer/asog-website), enhanced during the 2026 DOST-SEI Practical Training Program period from June 15, 2026 to July 24, 2026.

Production site: [https://asogtbi.com](https://asogtbi.com)

## Improvement Basis

The Website v2 enhancements were guided by the ASOG TBI Website v1 audit conducted in June 2026. The audit identified usability, accessibility, performance, content-management, security, deployment, and maintainability issues in the original website implementation, and those findings informed the improvements, fixes, and feature enhancements made in this repository.

Audit reference: [ASOG TBI Website Audit Report (June 2026).pdf](<docs/ASOG TBI Website Audit Report (June 2026).pdf>)

## Contributors

<table align="center" border="0" cellpadding="14" cellspacing="0">
  <tr>
    <td align="center" width="128" style="border:0;">
      <a href="https://github.com/ferenimedez-stab">
        <img src="https://wsrv.nl/?url=github.com/ferenimedez-stab.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="ferenimedez-stab">
        <br>
        <sub><b>ferenimedez-stab</b></sub>
      </a>
    </td>
    <td align="center" width="128" style="border:0;">
      <a href="https://github.com/jazz-lnz">
        <img src="https://wsrv.nl/?url=github.com/jazz-lnz.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="jazz-lnz">
        <br>
        <sub><b>jazz-lnz</b></sub>
      </a>
    </td>
    <td align="center" width="128" style="border:0;">
      <a href="https://github.com/johncarlonas">
        <img src="https://wsrv.nl/?url=github.com/johncarlonas.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="johncarlonas">
        <br>
        <sub><b>johncarlonas</b></sub>
      </a>
    </td>
    <td align="center" width="128" style="border:0;">
      <a href="https://github.com/Arrvsssogood">
        <img src="https://wsrv.nl/?url=github.com/Arrvsssogood.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="Arrvsssogood">
        <br>
        <sub><b>Arrvsssogood</b></sub>
      </a>
    </td>
    <td align="center" width="128" style="border:0;">
      <a href="https://github.com/mprestado">
        <img src="https://wsrv.nl/?url=github.com/mprestado.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="mprestado">
        <br>
        <sub><b>mprestado</b></sub>
      </a>
    </td>
    <td align="center" width="128" style="border:0;">
      <a href="https://github.com/jpyxs">
        <img src="https://wsrv.nl/?url=github.com/jpyxs.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="jpyxs">
        <br>
        <sub><b>jpyxs</b></sub>
      </a>
    </td>
  </tr>
</table>

## Functional Scope

- Public website for ASOG TBI information, programs, services, facilities, incubatees, news, organization profiles, contact forms, and legal pages.
- Incubatee application portal for application intake, duplicate-email checks, document uploads, confirmation email delivery, status review, and private revalidation links.
- Admin dashboard for content management, application review, contact-message handling, site settings, account management, and role-based operational workflows.
- Transactional email pipeline using Gmail API as the primary sender, with optional SMTP fallback for resilience.
- Google OAuth support for administrator login, administrator account linking, and Guess the Startup player sign-in.
- Score-based Google reCAPTCHA integration for public form protection.
- Guess the Startup game with profile setup, daily play sessions, scoring, anti-repeat checks, and leaderboard APIs.
- Notification system for administrators, including role-targeted and account-targeted notification visibility.
- Public-facing legal and OAuth reviewer pages for privacy, terms, and application identity verification.

## Tech Stack

- PHP `^8.2`
- CodeIgniter `^4.7`
- MySQL or MariaDB through CodeIgniter's `MySQLi` driver
- Composer for PHP dependencies
- Node.js and npm for frontend build tooling
- Tailwind CSS CLI `^4.2`
- Google API Client `^2.19`
- PHPUnit `^10.5`
- Local static assets, vendor JavaScript, and CSS served from `public/`

## Project Structure

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
|   |   |-- games/           Guess the Startup frontend assets
|   |   |-- img/             Brand, partner, facility, team, SDG, and static images
|   |   |-- js/              Public feature scripts and admin dashboard scripts
|   |   |-- loader/          ASOG landing loader assets and vendor files
|   |   `-- vendor/          Committed third-party frontend assets
|   |-- uploads/             Publicly served uploaded media
|   |-- index.php            Web front controller
|   |-- style.css            Compiled Tailwind output
|   |-- robots.txt           Search crawler rules
|   `-- favicon.ico          Site icon
|-- src/
|   `-- tailwind.css         Tailwind source stylesheet
|-- tests/                   PHPUnit tests and support files
|-- writable/                Runtime cache, logs, sessions, and protected uploads
|-- composer.json            PHP dependencies and scripts
|-- package.json             Frontend build scripts and Tailwind dependency
|-- phpunit.xml.dist         PHPUnit configuration template
`-- spark                    CodeIgniter CLI entrypoint
```

## Public Routes

The main public routes are:

- `/` and `/landing` - landing page
- `/about` and `/about/logo` - About page and logo redirect
- `/programs` - programs overview
- `/services` - services overview
- `/facilities` - facilities overview
- `/incubatees` and `/incubatees/cohort-{number}` - incubatee directory and cohort pages
- `/apply`, `/apply/form`, `/apply/form/thank-you` - application information and submission flow
- `/apply/revalidate/{token}` - private revalidation flow for submitted applications
- `/news` and `/news/{slug}` - news listing and article detail pages
- `/organization` - organization members and mentors
- `/contact` and `/contact/send` - contact page and contact form submission
- `/games/guess-the-startup` - Guess the Startup landing page
- `/games/guess-the-startup/play` - Guess the Startup play page
- `/games/guess-the-startup/leaderboard` - public leaderboard
- `/games/guess-the-startup/profile` - player profile setup
- `/privacy-policy`, `/terms-of-service`, and `/asog-tbi-website-app` - legal and Google OAuth reviewer-facing pages
- `/sitemap.xml` - sitemap

Lightweight API routes are provided for frontend features:

- `/api/sdgs`
- `/api/incubatees`
- `/api/games/guess-startup/leaderboard`
- `/api/games/guess-startup/start`
- `/api/games/guess-startup/submit`
- `/api/games/guess-startup/abandon`

Application uploads stored outside the web root are served through controlled upload routes under `/uploads/applications/...` and `/uploads/templates/...`.

## Feature Details By Section

### Public Website

- **Landing page** - presents the ASOG TBI brand, featured news hero slides, About preview, program highlights, selected incubatees, latest news, organization preview, calls to action, and optional landing-loader experience.
- **About page** - explains ASOG TBI, CSPC context, partner support, organization background, and brand/logo references.
- **Programs and services** - separates program storytelling from service offerings and includes the ALTITUDE 3D program experience for guided startup-development stages.
- **Facilities** - presents ASOG TBI spaces and facility media, including static facility galleries and service-specific facility context.
- **Incubatees** - lists published incubatees by cohort, supports cohort pages, SDG display, company information, contact links, logos, team members, and public profile data.
- **News** - lists published posts, supports article detail pages by slug, preserves slug redirect history, supports categories, and feeds the landing-page featured/hero content.
- **Organization** - displays leadership, staff, mentors, interns, and organization sections managed from the admin dashboard.
- **Contact** - provides public contact details and a form that stores messages, triggers admin notifications, and can send email notifications.
- **Legal and reviewer pages** - provide privacy policy, terms of service, and app information pages required for public trust and Google OAuth/Gmail API verification.

### Incubatee Application Flow

- Public users can open the application page, review instructions, and proceed to the application form.
- The form supports applicant information, startup details, team details, SDG alignment, file uploads, and Lean Canvas/template-related requirements.
- Email checks help prevent or manage duplicate application submissions according to current settings.
- Successful submissions route applicants to a thank-you page and can send confirmation email.
- Admin-reviewed applications can receive private revalidation links for controlled resubmission or update flows.
- Application windows, duplicate-email behavior, FAQ visibility, and Lean Canvas template behavior are managed from admin settings.

### Guess The Startup

- Public game landing page introduces the game and links to play/profile/leaderboard flows.
- Players can sign in with Google or complete a local profile flow depending on the current game path.
- The play API manages game sessions, guesses, abandonment, completion, scoring, and leaderboard results.
- The leaderboard exposes public rankings for the active play date.
- Admin settings control game visibility and availability.

### Admin Operations

- Admin screens use a protected dashboard shell with sidebar status, notifications, dirty-form protection, delete confirmations, custom selects, and feature-specific scripts.
- Posts management supports drafts, publishing, categories, image uploads, slug history, previews, featured content, and hero slide ordering.
- Incubatee management supports company profiles, cohorts, logos, white logos, team members, contacts, SDGs, publishing, and drag/reorder workflows.
- Applications management supports review queues, status updates, reviewer remarks, archiving, deletion, bulk actions, and email status updates.
- Messages management supports contact-message review, read/unread state, archiving, deletion, and bulk actions.
- Organization management supports sectioned member management, mentors, featured entries, modal editing, photo uploads, and ordering.
- FAQ management supports Apply-page FAQ content, section copy, ordering, publishing, and deletion.
- Settings management controls public application settings, homepage incubatee filtering, site experience toggles, intern visibility, game availability, password updates, and Lean Canvas template upload/deletion.
- Account management supports administrator creation, editing, activation state, roles, welcome email delivery, and Google account authorization/linking.
- Notifications are role-aware and can be marked read individually or in bulk.

## Admin Area

Admin login starts at:

```text
/asog-admin
```

After authentication, the protected admin dashboard is served under:

```text
/admin
```

The admin area supports:

- Dashboard summaries and sidebar status updates
- Posts and hero slide management
- Incubatee and cohort management
- Application review, remarks, status updates, archiving, and bulk actions
- Contact message review, read state, archiving, deletion, and bulk actions
- Organization member and mentor management
- Apply-page FAQ management
- Site settings, homepage controls, application window settings, and Lean Canvas template management
- Administrator account management
- Administrator Google account linking
- Role-targeted notifications and mark-read actions
- Superadmin-only Gmail API refresh-token setup route

## User Roles And Permissions

The application separates public users, game players, applicants, and administrators.

- **Public visitor** - can browse public pages, view incubatees/news/facilities/organization content, submit contact messages, access legal pages, and play public game flows when enabled.
- **Applicant** - can submit incubatee applications, upload required files, receive confirmation email, and use private revalidation links when an admin requests updated information.
- **Game player** - can complete Guess the Startup profile/sign-in flows, start a daily play session, submit guesses, abandon sessions, and appear on leaderboards when eligible.
- **Editor** - can access the admin dashboard for content-focused work such as posts, public incubatee records, cohorts, and Apply-page FAQs.
- **Admin** - includes editor capabilities and adds operational review tools for incubatee applications, contact messages, and organization member management.
- **Superadmin** - includes admin capabilities and adds site settings, administrator account management, game/application controls, Lean Canvas template management, Gmail API setup, and other high-impact configuration areas.

Role checks are enforced through protected admin route groups. Admin Google sign-in is not enough by itself; the account must also exist as an active administrator record with the appropriate role.

## Requirements

Install these before running the project locally:

- PHP 8.2 or newer
- Composer
- Node.js and npm
- MySQL or MariaDB
- PHP extensions required by CodeIgniter and this app, including `intl`, `mbstring`, `json`, `mysqlnd`, `curl`, `gd`, and `openssl`

## Local Setup

Install PHP dependencies:

```powershell
composer install
```

Install frontend dependencies:

```powershell
npm install
```

Create a local `.env` file and configure at least the app URL, database, encryption key, and any integrations needed for your local work.

Generate an encryption key if the environment does not already have one:

```powershell
php spark key:generate
```

Run migrations:

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

The default CodeIgniter development server is usually available at:

```text
http://localhost:8080
```

## Environment Configuration

Environment files and production secrets must not be committed. Keep `.env`, `.env.local`, and `.env.production` out of version control.

The application uses these environment variable groups:

- `app.*` - base URL, HTTPS behavior, and framework app settings
- `database.default.*` - primary database connection
- `googleOAuthClientId`, `googleOAuthClientSecret`, `googleOAuthAllowedDomains`, `googleOAuthRedirectUri`, `googleOAuthAccountRedirectUri`, `googleOAuthGameRedirectUri` - Google OAuth login and callback configuration
- `gmailApi.*` - Gmail API sender, recipient, OAuth client, refresh token, redirect URI, setup mode, and access-token cache settings
- `smtp.*` - optional SMTP fallback configuration
- `recaptcha.*` - Google reCAPTCHA project, keys, score threshold, enforcement mode, and allowed hostnames
- `emailBrand.*` - public URLs and branding values used in transactional email templates
- `deploymentMigrations.*` - temporary browser-accessible migration runner controls

Use placeholder values in shared examples. Real client IDs, client secrets, refresh tokens, API keys, database passwords, and SMTP passwords belong only in the appropriate local or production environment.

## Common Commands

Install PHP dependencies:

```powershell
composer install
```

Install frontend dependencies:

```powershell
npm install
```

Build CSS:

```powershell
npm.cmd run build:css
```

Watch CSS during frontend work:

```powershell
npm.cmd run watch:css
```

Run migrations:

```powershell
php spark migrate
```

Start the local server:

```powershell
php spark serve
```

Show routes:

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

## Email Delivery

Transactional email is sent through a wrapper that uses Gmail API first when it is configured. SMTP can be enabled as a fallback path for cases where Gmail API delivery is unavailable.

Gmail API setup requires:

- Gmail API enabled in Google Cloud
- OAuth Web Application credentials
- A redirect URI matching the environment
- A valid refresh token stored in `gmailApi.refreshToken`
- `gmailApi.setupEnabled = false` after setup is complete

SMTP fallback requires:

- `smtp.enabled = true`
- Host, user, password, port, crypto, sender email, and sender name values
- An app password when using Gmail SMTP

Do not use a normal Gmail account password for SMTP.

## Google OAuth

Google OAuth is used for:

- Admin login
- Admin self-service Google account linking
- Guess the Startup player sign-in

The Google Cloud OAuth client must include every callback URL used by the current environment. Admin Google sign-in is still controlled by the application's administrator records and role checks; a valid Google account alone is not sufficient for admin access.

## reCAPTCHA

The application supports score-based Google reCAPTCHA for public forms. Configure the project ID, site key, API key, minimum score, enforcement mode, and allowed hostnames before enabling enforcement in production.

## Frontend Assets

Tailwind CSS is authored in `src/tailwind.css` and compiled into `public/style.css`.

Run the CSS build whenever Tailwind source, utility classes, or relevant frontend styles change:

```powershell
npm.cmd run build:css
```

JavaScript and additional CSS assets are organized under `public/assets/`. Some vendor assets are intentionally committed under `public/assets/vendor/` and `public/assets/loader/vendor/` so public pages can run without relying on remote CDN delivery for those assets.

## Testing And QA

Before handing off changes, use the checks that match the touched area:

- `php -l path\to\file.php` for touched PHP files
- `node --check path\to\file.js` for touched JavaScript files
- `npm.cmd run build:css` when Tailwind or compiled CSS output is affected
- `vendor\bin\phpunit --no-coverage` for PHP unit coverage
- Manual browser checks for public pages, mobile layouts, admin workflows, forms, file uploads, and game interactions when those areas change

For production-facing changes, verify:

- The homepage and relevant public route load successfully
- Admin login and the touched admin screen work
- Forms submit correctly and show appropriate errors
- Transactional emails are delivered when the changed workflow sends email
- Uploaded public and protected files render or download through the correct route
- No local `.test`, `localhost`, or `127.0.0.1` URLs are active in production configuration

## Production Notes

The public production site is [https://asogtbi.com](https://asogtbi.com).

For future maintainers, keep production operations provider-neutral and avoid committing infrastructure-specific paths, credentials, IP addresses, access commands, or provider-specific operational details to the public repository.

Production expectations:

- The web server document root should point to the contents of `public/`, not to the repository root.
- Runtime folders under `writable/` must be writable by the PHP process.
- Public uploads under `public/uploads/` and protected uploads under `writable/uploads/` must be preserved during deployments.
- Production `.env` values must be configured on the server or deployment environment, never committed.
- CSS must be rebuilt before deployment when `src/tailwind.css`, Tailwind utility usage, or compiled frontend styling changes.
- New migrations should be reviewed, backed up against, and run intentionally.
- Public forms, application flow, contact email, admin login, file uploads, notifications, and the game should be smoke-tested after production changes.

Never run destructive migration commands on production unless a verified backup and rollback plan already exist:

```powershell
php spark migrate:refresh
php spark migrate:rollback
php spark db:seed
```

## Security And Maintenance

- Keep all secrets out of version control.
- Keep production `.env` values on the server.
- Disable temporary setup or migration routes after use.
- Keep `gmailApi.setupEnabled` disabled after refresh-token setup.
- Restrict Google OAuth domains only when the intended account policy is clear.
- Keep reCAPTCHA allowed hostnames aligned with the actual served domains.
- Preserve `writable/uploads/` and `public/uploads/` during deployment.
- Avoid committing generated logs, sessions, cache files, or local uploads.
- Review migrations before production deployment and confirm they preserve existing data.
- Keep transactional email templates and public legal pages aligned with OAuth and privacy review requirements.

## License

This project is proprietary software developed for ASOG Technology Business Incubator. All rights are reserved unless written permission is granted by ASOG Technology Business Incubator.

This repository may include third-party open-source components, including CodeIgniter and other dependencies, which remain governed by their respective licenses.

Contact:

```text
ASOG Technology Business Incubator
Camarines Sur Polytechnic Colleges
San Miguel, Nabua, Camarines Sur 4434
```

Email: [asogtbi@cspc.edu.ph](mailto:asogtbi@cspc.edu.ph)
