# ASOG TBI Website v2

## Team Setup and Development Guide

This repository is the development workspace for implementing the ASOG TBI Website Audit findings, fixes, improvements, and enhancements.

Repository:

```text
https://github.com/jpyxs/asog-website-v2
```

---

# Development Workflow

Before starting, make sure you have completed the local setup by following the instructions in `SETUP.md`.

#### 1. Check Your Assigned Issues
Go to:

`Repository → Issues`

Review the issue(s) assigned to you and understand the requirements before starting.

#### 2. Update Your Branch
Before working on an issue, always update your local repository:

```bash
git checkout develop
git pull

git checkout <your-branch>
git merge develop
```

Example:

```bash
git checkout develop
git pull

git checkout juan
git merge develop
```

This ensures your branch contains the latest changes from `develop`.

#### 3. Implement the Fix
Work only on your assigned issue(s) in your personal branch.

Do **not** commit directly to `develop` or `main`.

#### 4. Commit Your Changes
Use meaningful commit messages and include the issue number whenever possible.

Example:

```bash
git add .
git commit -m "Fix #12: add pause control to hero slideshow"
```

#### 5. Push Your Branch

```bash
git push
```

#### 6. Create a Pull Request
Create a Pull Request:

```text
your-branch → develop
```

Examples:

```text
juan → develop
maria → develop
jpyxs → develop
```

Do **not** create Pull Requests directly to `main`.

#### 7. Link the Issue
In the Pull Request description, include:

```text
Fixes #12
```

Replace `#12` with your actual issue number.

This automatically closes the issue once the Pull Request is merged.

#### Workflow Summary

```text
Assigned Issue
↓
Update develop
↓
Merge develop into your branch
↓
Implement fix
↓
Commit changes
↓
Push branch
↓
Create PR to develop
↓
Review & Merge
```

#### Important Reminders

- Do not commit directly to `develop`.
- Do not commit directly to `main`.
- Work only on your assigned issues unless coordinated otherwise.
- Always pull and merge the latest `develop` before starting work.
- Reference the issue number in commits and Pull Requests whenever possible.
- Move the issue status appropriately in the project board if assigned to you.

---

# Branch Structure

```text
main
└── develop
    ├── jpyxs
    ├── member1
    ├── member2
    └── member3
```

### Branch Purposes

**main**

* Production-ready branch
* Only approved and tested changes should reach this branch

**develop**

* Shared testing and integration branch
* All completed work is merged here first

**Personal Branch**

* Each developer works in their own branch
* All fixes and changes should be made here

Example:

```bash
git checkout develop
git pull
git checkout -b yourname
git push -u origin yourname
```

---

# Required Software

Install the following if not already available:

* Git
* PHP 8.3+
* Composer
* Node.js and npm
* MySQL or MariaDB

You may use any local development environment you prefer:

* Laragon
* XAMPP
* WAMP
* MAMP
* Native PHP/MySQL setup

Use whichever setup you are most comfortable with.

---

# Clone the Repository

```bash
git clone https://github.com/jpyxs/asog-website-v2.git
```

Enter the project directory:

```bash
cd asog-website-v2
```

---

# Install Dependencies

Install PHP dependencies:

```bash
composer install
```

Install Node.js dependencies:

```bash
npm install
```

---

# Database Setup

Create a database named:

```text
asogtbi
```

The required files are available here:

```text
https://bit.ly/3STS5Z6
```

Import:

```text
u559856532_asogtbi (1).sql
```

Use this file instead of:

```text
asogtbi_schema.sql
```

because it contains the latest working database structure and pre-seeded data.

---

# Environment File

The project `.env` file is also available in:

```text
https://bit.ly/3STS5Z6
```

Copy the provided `.env` file into the project root directory:

```text
asog-website-v2/.env
```

Do not upload or commit the `.env` file to GitHub.

---

# Generate Encryption Key

Run:

```bash
php spark key:generate
```

Expected output:

```text
Application's new encryption key was successfully set.
```

---

# Start the Development Server

Run:

```bash
php spark serve
```

Expected output:

```text
CodeIgniter development server started on http://localhost:8080
```

Open:

```text
http://localhost:8080
```

in your browser.

---

# Verify Setup

Verify that the following pages load properly:

* Home
* About
* Programs
* Facilities
* Incubatees
* News
* Contact

Verify that content is being loaded from the database.

---

# Daily Development Workflow

Update `develop`:

```bash
git checkout develop
git pull
```

Switch to your branch:

```bash
git checkout yourname
```

Merge the latest changes:

```bash
git merge develop
```

---

# Committing Changes

```bash
git add .
git commit -m "Describe your changes"
git push
```

Example:

```bash
git commit -m "Fix hero carousel autoplay timing"
```

---

# Pull Request Workflow

Create Pull Requests using:

```text
yourname
      ↓
develop
```

After testing and review:

```text
develop
      ↓
main
```

Do not create Pull Requests directly to `main`.

---

# Important Reminders

* Do not commit `.env`
* Do not commit credentials or secrets
* Do not work directly on `main`
* Test changes locally before submitting a Pull Request
* Document issues in the workbook before implementation
* Create GitHub Issues based on approved workbook entries
* All implementation work must go through `develop` before reaching `main`

```
```
