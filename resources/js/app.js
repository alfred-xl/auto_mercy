import Swiper from 'swiper';
import { A11y, Keyboard, Navigation, Pagination } from 'swiper/modules';

const trigger = document.querySelector('[data-mobile-menu-trigger]');
const menu = document.querySelector('[data-mobile-menu]');

if (trigger && menu) {
    const closeMenu = () => {
        menu.classList.add('hidden');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.querySelector('.sr-only').textContent = 'Open navigation';
        document.body.classList.remove('overflow-hidden');
    };

    const openMenu = () => {
        menu.classList.remove('hidden');
        trigger.setAttribute('aria-expanded', 'true');
        trigger.querySelector('.sr-only').textContent = 'Close navigation';
        document.body.classList.add('overflow-hidden');
        menu.querySelector('a')?.focus();
    };

    trigger.addEventListener('click', () => {
        if (trigger.getAttribute('aria-expanded') === 'true') {
            closeMenu();
        } else {
            openMenu();
        }
    });

    menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && trigger.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            trigger.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeMenu();
        }
    });
}

const desktopCarouselMedia = window.matchMedia('(min-width: 1024px)');
const reducedMotionMedia = window.matchMedia('(prefers-reduced-motion: reduce)');

document.querySelectorAll('[data-latest-cars-carousel]').forEach((carousel) => {
    let swiper = null;

    const wrapper = carousel.querySelector('.swiper-wrapper');
    const slides = carousel.querySelectorAll('.swiper-slide');
    const pagination = carousel.querySelector('[data-latest-cars-pagination]');
    const previous = carousel.querySelector('[data-latest-cars-previous]');
    const next = carousel.querySelector('[data-latest-cars-next]');

    const initializeCarousel = () => {
        if (desktopCarouselMedia.matches || swiper) {
            return;
        }

        swiper = new Swiper(carousel, {
            modules: [Navigation, Pagination, Keyboard, A11y],
            slidesPerView: 1.1,
            spaceBetween: 16,
            speed: reducedMotionMedia.matches ? 0 : 500,
            loop: false,
            watchOverflow: true,
            grabCursor: true,
            keyboard: {
                enabled: true,
                onlyInViewport: true,
            },
            navigation: {
                prevEl: previous,
                nextEl: next,
            },
            pagination: {
                el: pagination,
                clickable: true,
                bulletElement: 'button',
            },
            a11y: {
                enabled: true,
                prevSlideMessage: 'Previous vehicle',
                nextSlideMessage: 'Next vehicle',
                firstSlideMessage: 'This is the first vehicle',
                lastSlideMessage: 'This is the last vehicle',
                paginationBulletMessage: 'Go to vehicle {{index}}',
            },
            breakpoints: {
                768: {
                    slidesPerView: 2.1,
                    spaceBetween: 24,
                },
            },
        });
    };

    const destroyCarousel = () => {
        if (!swiper) {
            return;
        }

        swiper.destroy(true, true);
        swiper = null;

        carousel.removeAttribute('style');
        wrapper?.removeAttribute('style');
        slides.forEach((slide) => slide.removeAttribute('style'));
    };

    const syncCarousel = () => {
        if (desktopCarouselMedia.matches) {
            destroyCarousel();
        } else {
            initializeCarousel();
        }
    };

    syncCarousel();

    if (desktopCarouselMedia.addEventListener) {
        desktopCarouselMedia.addEventListener('change', syncCarousel);
    } else {
        desktopCarouselMedia.addListener(syncCarousel);
    }
});

document.querySelectorAll('[data-inventory-filter-form]').forEach((form) => {
    const make = form.querySelector('[data-inventory-make]');
    const model = form.querySelector('[data-inventory-model]');
    const announcement = form.querySelector('[data-model-announcement]');

    if (!make || !model) {
        return;
    }

    const syncModels = (announce = false) => {
        const selectedMake = make.value;
        const selectedModel = model.selectedOptions[0];
        const modelWasCleared = selectedModel?.value && selectedModel.dataset.make !== selectedMake;

        model.querySelectorAll('option[data-make]').forEach((option) => {
            const isCompatible = selectedMake !== '' && option.dataset.make === selectedMake;
            option.hidden = !isCompatible;
            option.disabled = !isCompatible;
        });

        if (modelWasCleared) {
            model.value = '';
        }

        model.disabled = selectedMake === '';

        if (announce && announcement) {
            announcement.textContent = modelWasCleared
                ? 'The previous model was cleared because it does not belong to the selected make.'
                : selectedMake === ''
                    ? 'Select a make before choosing a model.'
                    : 'Model options were updated for the selected make.';
        }
    };

    make.addEventListener('change', () => syncModels(true));
    form.addEventListener('reset', () => window.setTimeout(() => syncModels(false), 0));
    syncModels(false);
});

const inventoryDrawer = document.querySelector('[data-inventory-filter-drawer]');
const inventoryFilterTriggers = document.querySelectorAll('[data-inventory-filter-trigger]');

if (inventoryDrawer && inventoryFilterTriggers.length > 0) {
    const panel = inventoryDrawer.querySelector('[data-inventory-filter-panel]');
    const form = inventoryDrawer.querySelector('[data-inventory-filter-form]');
    const closeButtons = inventoryDrawer.querySelectorAll('[data-inventory-filter-close], [data-inventory-filter-cancel], [data-inventory-filter-backdrop]');
    let returnFocus = null;

    const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), summary, [tabindex]:not([tabindex="-1"])';

    const openDrawer = (trigger) => {
        returnFocus = trigger;
        inventoryDrawer.classList.remove('hidden');
        inventoryDrawer.hidden = false;
        inventoryDrawer.setAttribute('aria-hidden', 'false');
        inventoryFilterTriggers.forEach((button) => button.setAttribute('aria-expanded', 'true'));
        document.body.classList.add('overflow-hidden');
        inventoryDrawer.querySelector('[data-inventory-filter-close]')?.focus();
    };

    const closeDrawer = (discardChanges = true) => {
        if (inventoryDrawer.classList.contains('hidden')) {
            return;
        }

        if (discardChanges) {
            form?.reset();
        }

        inventoryDrawer.classList.add('hidden');
        inventoryDrawer.hidden = true;
        inventoryDrawer.setAttribute('aria-hidden', 'true');
        inventoryFilterTriggers.forEach((button) => button.setAttribute('aria-expanded', 'false'));
        document.body.classList.remove('overflow-hidden');
        returnFocus?.focus();
    };

    inventoryFilterTriggers.forEach((trigger) => trigger.addEventListener('click', () => openDrawer(trigger)));
    closeButtons.forEach((button) => button.addEventListener('click', () => closeDrawer(true)));

    inventoryDrawer.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeDrawer(true);
            return;
        }

        if (event.key !== 'Tab' || !panel) {
            return;
        }

        const focusable = [...panel.querySelectorAll(focusableSelector)].filter((element) => !element.hidden && element.getClientRects().length > 0);
        const first = focusable[0];
        const last = focusable.at(-1);

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last?.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first?.focus();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            closeDrawer(true);
        }
    });
}

document.querySelector('[data-inventory-sort]')?.addEventListener('change', (event) => {
    event.currentTarget.form?.requestSubmit();
});

const inventoryResults = document.querySelector('[data-inventory-results]');
const inventoryLoading = document.querySelector('[data-inventory-loading]');

document.querySelectorAll('[data-inventory-filter-form], [data-inventory-sort-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        inventoryResults?.setAttribute('aria-busy', 'true');
        inventoryLoading?.classList.remove('hidden');
        form.querySelectorAll('[data-inventory-filter-submit]').forEach((button) => {
            button.disabled = true;
        });
    });
});

const shareInventory = document.querySelector('[data-share-inventory]');

if (shareInventory) {
    const status = document.querySelector('[data-inventory-share-status]');

    const copyCurrentUrl = async () => {
        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(window.location.href);
            return;
        }

        const temporaryInput = document.createElement('textarea');
        temporaryInput.value = window.location.href;
        temporaryInput.style.position = 'fixed';
        temporaryInput.style.opacity = '0';
        document.body.appendChild(temporaryInput);
        temporaryInput.select();
        document.execCommand('copy');
        temporaryInput.remove();
    };

    shareInventory.addEventListener('click', async () => {
        try {
            if (navigator.share) {
                await navigator.share({ title: document.title, url: window.location.href });
                status.textContent = 'Inventory link shared.';
            } else {
                await copyCurrentUrl();
                status.textContent = 'Inventory link copied.';
            }
        } catch (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'The link could not be shared. Copy it from the address bar.';
            }
        }
    });
}

document.querySelectorAll('[data-save-vehicle]').forEach((button) => {
    const storageKey = `auto-mercy:saved:${button.dataset.saveVehicle}`;
    const outlineIcon = button.querySelector('[data-save-icon-outline]');
    const filledIcon = button.querySelector('[data-save-icon-filled]');

    const setSaved = (saved) => {
        button.setAttribute('aria-pressed', String(saved));
        button.setAttribute('aria-label', `${saved ? 'Remove' : 'Save'} ${button.getAttribute('aria-label').replace(/^(Save|Remove) /, '')}`);
        outlineIcon?.classList.toggle('hidden', saved);
        filledIcon?.classList.toggle('hidden', !saved);
    };

    try {
        setSaved(window.localStorage.getItem(storageKey) === 'true');
    } catch {
        setSaved(false);
    }

    button.addEventListener('click', () => {
        const saved = button.getAttribute('aria-pressed') !== 'true';
        setSaved(saved);

        try {
            window.localStorage.setItem(storageKey, String(saved));
        } catch {
            // The visual toggle remains available when storage is restricted.
        }
    });
});
