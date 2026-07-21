# Manual Hostinger Deploy Guide for ASOG Website v2

This guide assumes you cannot use Git on Hostinger. The goal is to deploy the v2 code without losing existing production data such as applications, contact messages, and uploaded applicant files.

## Golden Rules

1. Do not import your local database over production.
2. Do not delete or overwrite production `.env`.
3. Do not delete or overwrite production `writable/uploads/`.
4. Do not delete or overwrite production `public/uploads/` unless you intentionally want to replace uploaded public media.
5. Never run `php spark migrate:refresh`, `php spark migrate:rollback`, or any reset command on production.
6. Only run `php spark migrate` after taking a database backup.

## What Existing Data Lives Where

Production applications and messages are in the production database:

- `incubatee_applications`
- `contact_messages`
- admin/settings/content tables

Applicant uploaded files are stored under:

- `writable/uploads/applications/`
- `writable/uploads/templates/`

Public uploaded admin assets may be under:

- `public/uploads/`

Preserve these.

## Before You Touch Production

### 1. Back Up The Production Database

In Hostinger:

1. Open hPanel.
2. Go to Databases.
3. Open phpMyAdmin for the production database.
4. Click the production database name.
5. Click Export.
6. Choose Quick export and SQL.
7. Download the `.sql` file.

Name it something like:

```text
asogtbi-production-backup-YYYY-MM-DD-before-v2.sql
```

Keep this file safe.

### 2. Back Up Current Production Files

Using Hostinger File Manager:

1. Go to the folder where the current site lives.
2. Select the current project files.
3. Compress them into a zip.
4. Name it:

```text
production-files-before-v2-YYYY-MM-DD.zip
```

At minimum, separately back up:

```text
.env
writable/uploads/
public/uploads/
```

## Recommended Path For This First v2 Deploy

Production does not have v2 yet, so do a full v2 file deploy. SSH is helpful, but mostly for running commands after upload. You can upload the files with Hostinger File Manager or SFTP.

Your Hostinger root appears to already be arranged like this:

```text
ROOT/
  app/
  vendor/
  writable/
  .env
  composer.json
  composer.lock
  public_html/
```

That is a normal CodeIgniter layout. The browser only serves `public_html`, while app code, vendor code, and writable storage live one level above it.

Do not deploy into this folder:

```text
ROOT/asog-website/
```

That looks like an old/staging source folder. The live site is probably using:

```text
ROOT/app/
ROOT/vendor/
ROOT/writable/
ROOT/.env
ROOT/public_html/
```

This was confirmed by the production `public_html/index.php` content. It loads:

```php
require FCPATH . '../vendor/autoload.php';
$pathsConfig = FCPATH . '../app/Config/Paths.php';
```

That means `public_html` expects `vendor/` and `app/` to be one folder above it, at `ROOT/vendor/` and `ROOT/app/`.

If the real production file literally contains this:

```php
define('FCPATH', **DIR** . DIRECTORY_SEPARATOR);
```

that is wrong and should be:

```php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
```

It may only look wrong because of copy/paste formatting. In Hostinger File Manager, confirm it is actually `__DIR__`.

## Local To Hostinger Folder Map

Upload local folders/files to these production locations:

```text
LOCAL app/                  -> ROOT/app/
LOCAL vendor/               -> ROOT/vendor/
LOCAL composer.json         -> ROOT/composer.json
LOCAL composer.lock         -> ROOT/composer.lock
LOCAL spark                 -> ROOT/spark
LOCAL public/*              -> ROOT/public_html/*
LOCAL .env.production       -> ROOT/.env   (after filling Hostinger DB credentials)
```

Important notes:

- In Hostinger File Manager, `composer.json` may display as `composer`.
- `index.php` may display as `index`.
- `favicon.ico` may display as `favicon`.
- This is normal if file extensions are hidden.

Do not upload local `public/` as a folder inside `public_html/public/`. Upload the contents of local `public/` into `public_html/`.

For example:

```text
LOCAL public/index.php      -> ROOT/public_html/index.php
LOCAL public/style.css      -> ROOT/public_html/style.css
LOCAL public/assets/        -> ROOT/public_html/assets/
LOCAL public/favicon.ico    -> ROOT/public_html/favicon.ico
```

Create a zip from your local project, but exclude:

```text
.git/
.codex/
.agents/
node_modules/
.env
.env.local
.env.production
writable/cache/
writable/debugbar/
writable/logs/
writable/session/
writable/uploads/
HOSTINGER_MANUAL_DEPLOY_V2.md
```

Do not exclude `vendor/` unless you are sure you can run Composer on Hostinger. This project needs `vendor/` for CodeIgniter and Google API dependencies.

Usually also preserve production:

```text
ROOT/public_html/uploads/
ROOT/writable/uploads/
```

Upload the zip to Hostinger and extract it over the existing project files, but keep the production `.env` and upload folders.

Important: if production currently has only v1, you still preserve production `.env`, `writable/uploads/`, and `public/uploads/`. Everything else can be replaced by v2 code.

In your Hostinger structure, that means preserve:

```text
ROOT/.env
ROOT/writable/uploads/
ROOT/public_html/uploads/
```

Do not rely on `ROOT/asog-website/` for the live site. It looks like a repo/staging folder, not the folder currently serving the website.

## Env Files Created Locally

I created these local helper files:

```text
.env.local
.env.production
```

- `.env.local` is a copy of your current local Laragon env.
- `.env.production` is the production template to upload as `.env` after you fill in Hostinger database credentials.

Keep your current local `.env` unchanged so local development keeps working.

Before uploading `.env.production`, open it and replace:

```ini
database.default.hostname = HOSTINGER_DB_HOST
database.default.database = HOSTINGER_DB_NAME
database.default.username = HOSTINGER_DB_USER
database.default.password = 'HOSTINGER_DB_PASSWORD'
```

with the actual values from Hostinger hPanel.

Then upload it to the production project root and rename it on the server to:

```text
.env
```

## Production `.env` Checklist

On Hostinger, the production `.env` should have production values, not local Laragon values. Use `.env.production` as your starting point.

Check these carefully:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://asogtbi.com/'

database.default.hostname = HOSTINGER_DB_HOST
database.default.database = HOSTINGER_DB_NAME
database.default.username = HOSTINGER_DB_USER
database.default.password = 'HOSTINGER_DB_PASSWORD'
database.default.DBDriver = MySQLi
database.default.port = 3306
```

Do not upload your local `.env` or `.env.local` because they have:

```ini
app.baseURL = 'https://asog-website-v2.test/'
database.default.username = root
database.default.password =
```

Those are local-only.

## How To Upload Without Git

### Option 1: Hostinger File Manager

1. Zip the files locally.
2. Upload the zip in Hostinger File Manager.
3. Extract it into the project root.
4. Upload `.env.production` separately after filling in DB credentials, then rename it to `.env` on the server.
5. Confirm `writable/uploads/` and `public/uploads/` were not overwritten.

### Option 2: SFTP

Use FileZilla or WinSCP:

1. Connect using Hostinger SFTP credentials.
2. Upload the files/folders.
3. Skip `.env`.
4. Skip `writable/uploads/`.
5. Be careful with `public/uploads/`.

### Option 3: SSH Upload + Unzip

If Hostinger SSH is enabled:

1. Upload the zip through File Manager or SFTP.
2. SSH into the account.
3. Go to the project folder.
4. Extract the zip.

Example:

```bash
cd domains/asogtbi.com
unzip v2-deploy.zip
```

Your exact path may differ. In Hostinger, the site folder is often under:

```text
domains/asogtbi.com/public_html
```

or:

```text
public_html
```

## Run The Migration Safely

### Best Option: SSH

If SSH is available, after files are uploaded, SSH into Hostinger and run:

```bash
php spark migrate
```

This update adds one safe setting row:

```text
apply_show_faqs = 1
```

It does not delete applications or messages.

Because this is the first full v2 deploy, there may be other pending v2 migrations too. That is expected. `php spark migrate` applies missing migrations without clearing existing tables. It is not the same as a reset.

Then clear cache:

```bash
php spark cache:clear
```

If Hostinger uses a specific PHP binary, you may need:

```bash
php82 spark migrate
php82 spark cache:clear
```

or:

```bash
/usr/bin/php spark migrate
```

### If You Only Have File Manager: Browser Migration URL

This project already includes a controlled migration URL:

```text
https://asogtbi.com/deployment/run-migrations?token=YOUR_TOKEN
```

It only works when enabled in production `.env`.

Steps:

1. Back up the production database first.
2. Make sure these uploaded v2 files are present on Hostinger:

```text
ROOT/app/Controllers/Deployment.php
ROOT/app/Config/Routes.php
ROOT/app/Config/Filters.php
ROOT/app/Database/Migrations/
```

3. In production `.env`, set:

```ini
deploymentMigrations.enabled = true
deploymentMigrations.token = 'A_LONG_RANDOM_SECRET_TOKEN'
```

4. Visit this URL in your browser, replacing the token:

```text
https://asogtbi.com/deployment/run-migrations?token=A_LONG_RANDOM_SECRET_TOKEN
```

5. A successful run should show:

```text
Migrations completed successfully.
```

6. Immediately edit production `.env` again and set:

```ini
deploymentMigrations.enabled = false
```

7. If the page still seems cached, delete files inside:

```text
ROOT/writable/cache/
```

Do not delete the `writable/cache/` folder itself. Leave `index.html` if it exists.

Important: do not leave `deploymentMigrations.enabled = true`.

## After Deploy Checks

Open these pages:

```text
https://asogtbi.com/
https://asogtbi.com/apply
https://asogtbi.com/apply/form
https://asogtbi.com/contact
https://asogtbi.com/asog-admin
```

Admin checks:

1. Log into admin.
2. Open Settings.
3. Find `Show apply FAQs`.
4. Toggle off and save.
5. Open `/apply` in a private/incognito tab.
6. Confirm Benefits + CTA split layout appears.
7. Toggle back on if desired.
8. Confirm applications and messages are still visible in admin.

## If Something Breaks

Do not panic.

1. Check `writable/logs/` for the newest log file.
2. Restore only files first if it is a code/layout error.
3. Restore the database only if the database itself was damaged.

Common fixes:

```bash
php spark cache:clear
```

If CSS looks old, hard refresh the browser:

```text
Ctrl + F5
```

## What Not To Do

Do not run:

```bash
php spark migrate:refresh
php spark migrate:rollback
php spark db:seed
```

Do not upload:

```text
.env from local
local database dump
writable/uploads from local over production
```

## Recommended For Your Current Situation

Because production does not have v2 yet and you are worried about production v1 data, I recommend this order:

1. Back up production database.
2. Back up production files.
3. Fill `.env.production` with Hostinger DB credentials.
4. Zip local v2 files excluding `.env`, `.env.local`, `.env.production`, `node_modules`, `.git`, and `writable/uploads`.
5. Upload and extract the v2 zip over production files.
6. Upload `.env.production` separately and rename it to `.env` on production.
7. Preserve `writable/uploads/` and `public/uploads/`.
8. SSH into Hostinger.
9. Run `php spark migrate`.
10. Run `php spark cache:clear`.
11. Test `/apply`, `/apply/form`, admin applications, and admin messages.
