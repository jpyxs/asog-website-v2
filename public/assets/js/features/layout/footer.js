document.addEventListener('DOMContentLoaded', function () {
    var footer = document.querySelector('.site-footer');
    if (!footer) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || typeof gsap === 'undefined') {
        return;
    }

    var logo = footer.querySelector('.ft-logo');
    var tagline = footer.querySelector('.ft-tagline');
    var headings = footer.querySelectorAll('.ft-heading');
    var linkItems = footer.querySelectorAll('.ft-links li');
    var contactItems = footer.querySelectorAll('.ft-contact-list li');
    var socialLinks = footer.querySelectorAll('.ft-social-link');
    var bottom = footer.querySelector('.ft-bottom');

    var targets = [];

    if (logo) targets.push(logo);
    if (tagline) targets.push(tagline);
    headings.forEach(function (item) { targets.push(item); });
    linkItems.forEach(function (item) { targets.push(item); });
    contactItems.forEach(function (item) { targets.push(item); });
    socialLinks.forEach(function (item) { targets.push(item); });
    if (bottom) targets.push(bottom);

    if (!targets.length) return;

    var tl = gsap.timeline({
        defaults: {
            ease: 'power2.out'
        }
    });

    tl.set(targets, {
        opacity: 0,
        y: 12
    });

    if (logo) {
        tl.to(logo, {
            opacity: 1,
            y: 0,
            duration: 0.32
        });
    }

    if (tagline) {
        tl.to(tagline, {
            opacity: 1,
            y: 0,
            duration: 0.28
        }, '-=0.16');
    }

    if (headings.length) {
        tl.to(headings, {
            opacity: 1,
            y: 0,
            duration: 0.24,
            stagger: 0.05
        }, '-=0.12');
    }

    if (linkItems.length) {
        tl.to(linkItems, {
            opacity: 1,
            y: 0,
            duration: 0.22,
            stagger: 0.035
        }, '-=0.08');
    }

    if (contactItems.length) {
        tl.to(contactItems, {
            opacity: 1,
            y: 0,
            duration: 0.22,
            stagger: 0.035
        }, '-=0.08');
    }

    if (socialLinks.length) {
        tl.to(socialLinks, {
            opacity: 1,
            y: 0,
            duration: 0.22,
            stagger: 0.04
        }, '-=0.1');
    }

    if (bottom) {
        tl.to(bottom, {
            opacity: 1,
            y: 0,
            duration: 0.24
        }, '-=0.06');
    }
});