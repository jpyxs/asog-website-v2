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

Production site: `https://asogtbi.com`

## Contributors

<table>
  <tr>
    <td align="center" width="110">
      <a href="https://github.com/ferenimedez-stab">
        <img src="https://wsrv.nl/?url=github.com/ferenimedez-stab.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="ferenimedez-stab">
        <br>
        <sub><b>ferenimedez-stab</b></sub>
      </a>
    </td>
    <td align="center" width="110">
      <a href="https://github.com/jazz-lnz">
        <img src="https://wsrv.nl/?url=github.com/jazz-lnz.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="jazz-lnz">
        <br>
        <sub><b>jazz-lnz</b></sub>
      </a>
    </td>
    <td align="center" width="110">
      <a href="https://github.com/johncarlonas">
        <img src="https://wsrv.nl/?url=github.com/johncarlonas.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="johncarlonas">
        <br>
        <sub><b>johncarlonas</b></sub>
      </a>
    </td>
    <td align="center" width="110">
      <a href="https://github.com/Arrvsssogood">
        <img src="https://wsrv.nl/?url=github.com/Arrvsssogood.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="Arrvsssogood">
        <br>
        <sub><b>Arrvsssogood</b></sub>
      </a>
    </td>
    <td align="center" width="110">
      <a href="https://github.com/mprestado">
        <img src="https://wsrv.nl/?url=github.com/mprestado.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="mprestado">
        <br>
        <sub><b>mprestado</b></sub>
      </a>
    </td>
    <td align="center" width="110">
      <a href="https://github.com/jpyxs">
        <img src="https://wsrv.nl/?url=github.com/jpyxs.png%3Fsize%3D160&w=80&h=80&fit=cover&mask=circle" width="80" height="80" alt="jpyxs">
        <br>
        <sub><b>jpyxs</b></sub>
      </a>
    </td>
  </tr>
</table>

## Application Areas

- Public website for ASOG TBI information, programs, services, facilities, incubatees, news, organization profiles, contact forms, and legal pages.
- Incubatee application portal with application submission, email checks, thank-you flow, uploaded document handling, and private revalidation links.
- Admin dashboard for managing posts, incubatees, applications, contact messages, organization members, FAQs, homepage settings, game visibility, and administrator accounts.
- Transactional email pipeline using Gmail API as the primary sender, with optional SMTP fallback.
- Google OAuth support for administrator login, administrator account linking, and the Guess the Startup game.
- Score-based Google reCAPTCHA integration for public form protection.
- Guess the Startup game with profile setup, daily play, leaderboard, and supporting API endpoints.
- Notification system for administrators, including role-targeted and account-targeted notification visibility.

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

The role levels used by the admin routes are:

- `editor` - content-focused management access
- `admin` - operational management access
- `superadmin` - settings, account management, Gmail setup, and full administrative access

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

## Deployment Notes

The production domain is:

```text
https://asogtbi.com
```

The Hostinger production layout uses a CodeIgniter structure where application code lives outside the public web directory and public assets live in `public_html`.

Typical mapping:

```text
app/                  -> production app/
vendor/               -> production vendor/
writable/             -> production writable/
public/*              -> production public_html/*
composer.json         -> production composer.json
composer.lock         -> production composer.lock
spark                 -> production spark
```

Do not overwrite production `.env` with local values. Do not overwrite production uploads unless intentionally migrating uploaded files.

Safe deployment flow:

1. Back up production files and database.
2. Build CSS locally if frontend styles changed.
3. Upload only the changed application files and public assets, or deploy the reviewed release package.
4. Run `php spark migrate` only when new migrations are part of the release.
5. Clear framework cache and any server-level page cache.
6. Smoke-test public pages, admin access, forms, uploads, and email workflows.

Never run destructive migration commands on production unless a rollback plan and verified backup are already in place:

```powershell
php spark migrate:refresh
php spark migrate:rollback
php spark db:seed
```

Use direct file-cache cleanup only when `php spark cache:clear` is not available in the target hosting layout.

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
asogtbi@cspc.edu.ph
```
