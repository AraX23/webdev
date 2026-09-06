document.addEventListener('DOMContentLoaded', () => {
   /* ---- Nav Bar Additons ----------*/
   let lastScrollY = window.scrollY;
const header = document.querySelector('.site-header');

window.addEventListener('scroll', () => {
    const currentScrollY = window.scrollY;

    if (currentScrollY > lastScrollY && currentScrollY > 100) {
        // scrolling down — hide the header
        header.style.transform = 'translateY(-100%)';
    } else {
        // scrolling up — show the header
        header.style.transform = 'translateY(0)';
    }

    lastScrollY = currentScrollY;
});
   
    /* ---------- Mobile menu toggle ---------- */
    const hamburger = document.getElementById('hamburger');
    const mobileNav = document.getElementById('mobileNav');

    if (hamburger && mobileNav) {
        hamburger.addEventListener('click', () => {
            const isOpen = mobileNav.classList.toggle('is-open');
            hamburger.setAttribute('aria-expanded', String(isOpen));
            hamburger.setAttribute('aria-label', isOpen ? 'Close menu' : 'Open menu');
        });

        // close mobile menu after tapping a link
        mobileNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileNav.classList.remove('is-open');
                hamburger.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* ---------- Desktop dropdowns (Aspirants / Committees) ---------- */
    const dropdownParents = document.querySelectorAll('.has-dropdown');

    dropdownParents.forEach(parent => {
        const trigger = parent.querySelector('.nav-pill');
        if (!trigger) return;

        const closeDropdown = () => {
            parent.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
        };

        const toggleDropdown = (e) => {
            e.stopPropagation();
            const isOpen = parent.classList.contains('is-open');

            // close any other open dropdown first
            dropdownParents.forEach(p => { if (p !== parent) p.classList.remove('is-open'); });

            parent.classList.toggle('is-open', !isOpen);
            trigger.setAttribute('aria-expanded', String(!isOpen));
        };

        trigger.addEventListener('click', toggleDropdown);
        trigger.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDropdown();
        });
    });

    // click outside closes any open dropdown
    document.addEventListener('click', () => {
        dropdownParents.forEach(p => {
            p.classList.remove('is-open');
            const trigger = p.querySelector('.nav-pill');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
        });
    });

    /* ---------- Scroll-reveal for category & testimonial cards ---------- */
    const revealTargets = document.querySelectorAll('.category-card, .testimonial-card');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (revealTargets.length) {
        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            revealTargets.forEach(el => el.classList.add('is-visible'));
        } else {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.2 });

            revealTargets.forEach(el => observer.observe(el));
        }
    }

    /* ---------- Hero search: basic client-side guard ---------- */
    const heroSearch = document.querySelector('.hero-search');
    if (heroSearch) {
        heroSearch.addEventListener('submit', (e) => {
            const input = heroSearch.querySelector('input[name="q"]');
            if (input && !input.value.trim()) {
                e.preventDefault();
                input.focus();
            }
        });
    }

    /* ---------- Committee carousel ---------- */
    const carousel = document.querySelector('.carousel');
    if (carousel) {
        let committees = [];
        try {
            committees = JSON.parse(carousel.dataset.committees || '[]');
        } catch (err) {
            committees = [];
        }

        if (committees.length) {
            // start on the middle item (Technicals, per the mockup) when there
            // are exactly 3; otherwise just start at the first item
            let current = committees.length === 3 ? 1 : 0;

            const prevBtn = document.querySelector('.carousel-arrow--prev');
            const nextBtn = document.querySelector('.carousel-arrow--next');
            const prevSlide = carousel.querySelector('[data-role="prev"]');
            const centerSlide = carousel.querySelector('[data-role="center"]');
            const nextSlide = carousel.querySelector('[data-role="next"]');
            const titleEl = document.getElementById('carouselTitle');
            const descEl = document.getElementById('carouselDesc');

            const wrap = (i) => ((i % committees.length) + committees.length) % committees.length;

            const fillSlide = (slideEl, item) => {
                if (!slideEl || !item) return;
                const img = slideEl.querySelector('img');
                const label = slideEl.querySelector('.carousel-slide-label');
                if (img) {
                    img.src = item.image;
                    img.alt = item.title;
                }
                if (label) label.textContent = item.title;
            };

            const render = () => {
                const prevItem = committees[wrap(current - 1)];
                const centerItem = committees[wrap(current)];
                const nextItem = committees[wrap(current + 1)];

                fillSlide(prevSlide, prevItem);
                fillSlide(centerSlide, centerItem);
                fillSlide(nextSlide, nextItem);

                if (titleEl) titleEl.textContent = centerItem.title;
                if (descEl) descEl.textContent = centerItem.desc;
            };

            const goTo = (index) => {
                current = wrap(index);
                render();
            };

            if (prevBtn) prevBtn.addEventListener('click', () => goTo(current - 1));
            if (nextBtn) nextBtn.addEventListener('click', () => goTo(current + 1));
            if (prevSlide) prevSlide.addEventListener('click', () => goTo(current - 1));
            if (nextSlide) nextSlide.addEventListener('click', () => goTo(current + 1));

            render();
        }
    }
});
