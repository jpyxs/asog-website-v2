<link rel="stylesheet" href="<?= base_url('assets/games/guess-startup/css/play.css') ?>">

<?php $player = is_array($player ?? null) ? $player : []; ?>

<main id="main">
    <section class="gsp-root gsp-root-profile" data-navhint="light">
        <canvas id="gsThreeCanvas" class="gsp-canvas" aria-hidden="true"></canvas>

        <div class="gsp-scenery" aria-hidden="true">
            <div class="gsp-sun"></div>
            <div class="gsp-cloud-layer">
                <span class="gsp-cloud c1"></span>
                <span class="gsp-cloud c2"></span>
                <span class="gsp-cloud c3"></span>
            </div>
            <div class="gsp-mountain m1"></div>
            <div class="gsp-mountain m2"></div>
            <div class="gsp-mountain m3"></div>
            <div class="gsp-ground"></div>
        </div>

        <div class="gsp-shell">
            <a href="<?= site_url('games/guess-the-startup') ?>" class="gsp-back gsp-back-link">Back to Menu</a>

            <section class="gsp-gate gsp-profile-card">
                <h1 class="gsp-title">Complete Your Player Profile</h1>
                <p class="gsp-subtitle">First, middle, and last name plus school are required before joining today&apos;s Startup Hunt round.</p>
                <div class="gsp-profile-info-row">
                    <p class="gsp-gate-note">Your data is validated and sanitized before save. Name + school must be unique to protect leaderboard fairness.</p>
                </div>

                <?php $error = session('gs_error'); ?>
                <?php $notice = session('gs_notice'); ?>
                <?php $success = session('gs_success'); ?>

                <?php if (! empty($error)): ?>
                    <div class="gs-feedback-bad"><?= esc($error) ?></div>
                <?php endif; ?>
                <?php if (! empty($notice)): ?>
                    <div class="gs-feedback-note"><?= esc($notice) ?></div>
                <?php endif; ?>
                <?php if (! empty($success)): ?>
                    <div class="gs-feedback-good"><?= esc($success) ?></div>
                <?php endif; ?>

                <form action="<?= site_url('games/guess-the-startup/profile') ?>" method="post" class="gsp-profile-form" novalidate>
                    <?= csrf_field() ?>

                    <?php $profileMeta = is_array($profileMeta ?? null) ? $profileMeta : []; ?>
                    <?php $errors = is_array($errors ?? null) ? $errors : []; ?>
                    <?php $schoolOptions = [
                        'Camarines Sur Polytechnic Colleges',
                        'Naga College Foundation, Inc.',
                        'Sorsogon State University',
                        'STI College Legazpi',
                        'University of Saint Anthony',
                    ]; ?>
                    <?php $storedSchool = trim((string) ($profileMeta['school'] ?? ($player['school'] ?? ''))); ?>
                    <?php $isListedSchool = in_array($storedSchool, $schoolOptions, true); ?>
                    <?php $schoolSelection = (string) old('school', $isListedSchool ? $storedSchool : ($storedSchool !== '' ? 'Others' : '')); ?>
                    <?php $defaultSchoolOther = $isListedSchool ? '' : $storedSchool; ?>
                    <?php $schoolOther = (string) old('school_other', (string) ($profileMeta['school_other'] ?? $defaultSchoolOther)); ?>
                    <?php $showSchoolOther = $schoolSelection === 'Others'; ?>

                    <div class="gsp-form-grid">
                        <div class="gsp-form-row">
                            <label for="first_name">First Name</label>
                            <input
                                id="first_name"
                                name="first_name"
                                type="text"
                                maxlength="60"
                                value="<?= esc(old('first_name', (string) ($profileMeta['first_name'] ?? ''))) ?>"
                                placeholder="First name">
                            <p class="gsp-form-error" data-error-for="first_name"><?= esc((string) ($errors['first_name'] ?? '')) ?></p>
                        </div>

                        <div class="gsp-form-row">
                            <label for="middle_name">Middle Name</label>
                            <input
                                id="middle_name"
                                name="middle_name"
                                type="text"
                                maxlength="60"
                                value="<?= esc(old('middle_name', (string) ($profileMeta['middle_name'] ?? ''))) ?>"
                                placeholder="Middle name">
                            <p class="gsp-form-error" data-error-for="middle_name"><?= esc((string) ($errors['middle_name'] ?? '')) ?></p>
                        </div>

                        <div class="gsp-form-row">
                            <label for="last_name">Last Name</label>
                            <input
                                id="last_name"
                                name="last_name"
                                type="text"
                                maxlength="60"
                                value="<?= esc(old('last_name', (string) ($profileMeta['last_name'] ?? ''))) ?>"
                                placeholder="Last name">
                            <p class="gsp-form-error" data-error-for="last_name"><?= esc((string) ($errors['last_name'] ?? '')) ?></p>
                        </div>
                    </div>

                    <div class="gsp-form-row">
                        <label for="school">School</label>
                        <select id="school" name="school">
                            <option value="">Select your school</option>
                            <?php foreach ($schoolOptions as $schoolOption): ?>
                                <option value="<?= esc($schoolOption) ?>" <?= $schoolSelection === $schoolOption ? 'selected' : '' ?>>
                                    <?= esc($schoolOption) ?>
                                </option>
                            <?php endforeach; ?>
                            <option value="Others" <?= $schoolSelection === 'Others' ? 'selected' : '' ?>>Others</option>
                        </select>
                        <p class="gsp-form-error" data-error-for="school"><?= esc((string) ($errors['school'] ?? '')) ?></p>
                    </div>

                    <div class="gsp-form-row gsp-school-other-row<?= $showSchoolOther ? '' : ' gsp-form-row-hidden' ?>" aria-hidden="<?= $showSchoolOther ? 'false' : 'true' ?>">
                        <label for="school_other">Other School</label>
                        <input
                            id="school_other"
                            name="school_other"
                            type="text"
                            maxlength="190"
                            value="<?= esc($schoolOther) ?>"
                            placeholder="Enter your school name">
                        <p class="gsp-form-error" data-error-for="school_other"><?= esc((string) ($errors['school_other'] ?? '')) ?></p>
                    </div>

                    <div class="gsp-profile-actions">
                        <button type="submit" class="gs-btn gs-btn-primary">Save Profile</button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/three@0.161.0/build/three.min.js" defer></script>
<script src="<?= base_url('assets/games/guess-startup/js/three-bg.js') ?>" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.gsp-profile-form');
    if (!form) {
        return;
    }

    const requiredFields = [
        { name: 'first_name', label: 'First name' },
        { name: 'last_name', label: 'Last name' },
        { name: 'school', label: 'School' }
    ];

    const namePattern = /^[\p{L}\p{M}][\p{L}\p{M}\s'.-]*$/u;

    const getInput = function (name) {
        return form.querySelector('[name="' + name + '"]');
    };

    const getErrorEl = function (name) {
        return form.querySelector('[data-error-for="' + name + '"]');
    };

    const setError = function (name, message) {
        const errorEl = getErrorEl(name);
        if (!errorEl) {
            return;
        }

        errorEl.textContent = message;
    };

    const validateRequiredField = function (field, showOnFocus) {
        const input = getInput(field.name);
        if (!input) {
            return true;
        }

        const value = String(input.value || '').trim();
        if (value === '') {
            if (showOnFocus || document.activeElement !== input) {
                setError(field.name, field.label + ' is required.');
            }
            return false;
        }

        setError(field.name, '');
        return true;
    };

    const validateNamePartFormat = function (fieldName, label, showOnFocus, required) {
        const input = getInput(fieldName);
        if (!input) {
            return true;
        }

        const value = String(input.value || '').trim();
        if (value === '') {
            if (!required) {
                setError(fieldName, '');
                return true;
            }

            if (showOnFocus || document.activeElement !== input) {
                setError(fieldName, label + ' is required.');
            }
            return false;
        }

        if (!namePattern.test(value)) {
            if (showOnFocus || document.activeElement !== input) {
                setError(fieldName, 'Enter a valid ' + label.toLowerCase() + ' (letters, spaces, apostrophe, dot, and dash only).');
            }
            return false;
        }

        setError(fieldName, '');
        return true;
    };

    const schoolInput = getInput('school');
    const schoolOtherInput = getInput('school_other');
    const schoolOtherRow = form.querySelector('.gsp-school-other-row');

    const toggleSchoolOther = function () {
        if (!schoolInput || !schoolOtherRow) {
            return;
        }

        const show = String(schoolInput.value || '') === 'Others';
        schoolOtherRow.classList.toggle('gsp-form-row-hidden', !show);
        schoolOtherRow.setAttribute('aria-hidden', show ? 'false' : 'true');

        if (!show && schoolOtherInput) {
            schoolOtherInput.value = '';
            setError('school_other', '');
        }
    };

    const validateSchoolOther = function (showOnFocus) {
        if (!schoolInput || !schoolOtherInput) {
            return true;
        }

        if (String(schoolInput.value || '') !== 'Others') {
            setError('school_other', '');
            return true;
        }

        const value = String(schoolOtherInput.value || '').trim();
        if (value === '') {
            if (showOnFocus || document.activeElement !== schoolOtherInput) {
                setError('school_other', 'Other school is required.');
            }
            return false;
        }

        setError('school_other', '');
        return true;
    };

    requiredFields.forEach(function (field) {
        const input = getInput(field.name);
        if (!input) {
            return;
        }

        input.addEventListener('focus', function () {
            validateRequiredField(field, true);
        });

        input.addEventListener('input', function () {
            validateRequiredField(field, false);
            if (field.name === 'first_name' || field.name === 'last_name') {
                validateNamePartFormat(field.name, field.label, false, true);
            }
        });

        input.addEventListener('blur', function () {
            validateRequiredField(field, true);
            if (field.name === 'first_name' || field.name === 'last_name') {
                validateNamePartFormat(field.name, field.label, true, true);
            }
        });
    });

    const middleNameInput = getInput('middle_name');
    if (middleNameInput) {
        middleNameInput.addEventListener('input', function () {
            validateNamePartFormat('middle_name', 'Middle name', false, false);
        });

        middleNameInput.addEventListener('blur', function () {
            validateNamePartFormat('middle_name', 'Middle name', true, false);
        });
    }

    if (schoolInput) {
        schoolInput.addEventListener('change', function () {
            toggleSchoolOther();
            validateRequiredField({ name: 'school', label: 'School' }, true);
            validateSchoolOther(true);
        });
    }

    if (schoolOtherInput) {
        schoolOtherInput.addEventListener('focus', function () {
            validateSchoolOther(true);
        });

        schoolOtherInput.addEventListener('input', function () {
            validateSchoolOther(false);
        });

        schoolOtherInput.addEventListener('blur', function () {
            validateSchoolOther(true);
        });
    }

    toggleSchoolOther();

    form.addEventListener('submit', function (event) {
        let firstInvalid = null;

        requiredFields.forEach(function (field) {
            const valid = validateRequiredField(field, true);
            if (!valid && !firstInvalid) {
                firstInvalid = getInput(field.name);
            }

            if ((field.name === 'first_name' || field.name === 'last_name') && firstInvalid === null) {
                const nameValid = validateNamePartFormat(field.name, field.label, true, true);
                if (!nameValid && !firstInvalid) {
                    firstInvalid = getInput(field.name);
                }
            }
        });

        const middleNameValid = validateNamePartFormat('middle_name', 'Middle name', true, false);
        if (!middleNameValid && !firstInvalid) {
            firstInvalid = getInput('middle_name');
        }

        const schoolOtherValid = validateSchoolOther(true);
        if (!schoolOtherValid && !firstInvalid) {
            firstInvalid = getInput('school_other');
        }

        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();
        }
    });
});
</script>
