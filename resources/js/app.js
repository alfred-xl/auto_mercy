import Swiper from 'swiper';
import { A11y, Autoplay, Keyboard, Navigation, Pagination } from 'swiper/modules';

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

const revealElements = Array.from(document.querySelectorAll('[data-reveal]'));

if (revealElements.length > 0) {
    if (reducedMotionMedia.matches || !('IntersectionObserver' in window)) {
        revealElements.forEach((element) => element.classList.add('is-revealed'));
    } else {
        document.documentElement.classList.add('reveal-ready');

        document.querySelectorAll('[data-reveal-group]').forEach((group) => {
            Array.from(group.children)
                .filter((child) => child.matches('[data-reveal]'))
                .forEach((child, index) => {
                    if (!child.dataset.revealDelay) {
                        child.style.setProperty('--reveal-delay', `${Math.min(index * 90, 360)}ms`);
                    }
                });
        });

        revealElements.forEach((element) => {
            if (element.dataset.revealDelay) {
                element.style.setProperty('--reveal-delay', `${element.dataset.revealDelay}ms`);
            }
        });

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            });
        }, {
            rootMargin: '0px 0px -8% 0px',
            threshold: 0.12,
        });

        revealElements.forEach((element) => revealObserver.observe(element));
    }
}

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

const floatingWhatsapp = document.querySelector('[data-floating-whatsapp]');

if (floatingWhatsapp) {
    const collisionTargets = [...document.querySelectorAll('[data-floating-whatsapp-avoid]')]
        .filter((target) => target !== floatingWhatsapp);
    let collisionFrame;

    const updateFloatingWhatsappPosition = () => {
        const viewportMargin = window.innerWidth >= 640 ? 24 : 16;
        const collisionGap = 12;
        const width = floatingWhatsapp.offsetWidth;
        const height = floatingWhatsapp.offsetHeight;
        const baseRect = {
            left: window.innerWidth - viewportMargin - width,
            right: window.innerWidth - viewportMargin,
            top: window.innerHeight - viewportMargin - height,
            bottom: window.innerHeight - viewportMargin,
        };
        let requiredLift = 0;

        for (let pass = 0; pass < collisionTargets.length; pass += 1) {
            const currentTop = baseRect.top - requiredLift;
            const currentBottom = baseRect.bottom - requiredLift;
            let foundCollision = false;

            collisionTargets.forEach((target) => {
                const targetRect = target.getBoundingClientRect();
                const overlapsHorizontally = baseRect.right > targetRect.left - collisionGap
                    && baseRect.left < targetRect.right + collisionGap;
                const overlapsVertically = currentBottom > targetRect.top - collisionGap
                    && currentTop < targetRect.bottom + collisionGap;

                if (!overlapsHorizontally || !overlapsVertically) {
                    return;
                }

                requiredLift = Math.max(
                    requiredLift,
                    baseRect.bottom - targetRect.top + collisionGap,
                );
                foundCollision = true;
            });

            if (!foundCollision) {
                break;
            }
        }

        const maximumVisibleLift = Math.max(0, baseRect.top - viewportMargin);
        const cannotFitWithoutCoveringContent = requiredLift > maximumVisibleLift;

        floatingWhatsapp.classList.toggle('is-collision-hidden', cannotFitWithoutCoveringContent);
        floatingWhatsapp.style.setProperty(
            '--floating-whatsapp-lift',
            `${Math.min(requiredLift, maximumVisibleLift)}px`,
        );
    };

    const scheduleFloatingWhatsappUpdate = () => {
        window.cancelAnimationFrame(collisionFrame);
        collisionFrame = window.requestAnimationFrame(updateFloatingWhatsappPosition);
    };

    updateFloatingWhatsappPosition();
    window.addEventListener('load', scheduleFloatingWhatsappUpdate);
    window.addEventListener('resize', scheduleFloatingWhatsappUpdate);
    window.addEventListener('scroll', scheduleFloatingWhatsappUpdate, { passive: true });
}

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

document.querySelectorAll('[data-share-vehicle]').forEach((button) => {
    const label = button.querySelector('[data-share-vehicle-label]');
    const status = button.parentElement?.querySelector('[data-share-vehicle-status]');
    let resetLabelTimeout;

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
        const copied = document.execCommand('copy');
        temporaryInput.remove();

        if (!copied) {
            throw new Error('Copy command failed.');
        }
    };

    const announce = (message) => {
        if (label) {
            label.textContent = message;
            window.clearTimeout(resetLabelTimeout);
            resetLabelTimeout = window.setTimeout(() => {
                label.textContent = 'Share';
            }, 2500);
        }

        if (status) {
            status.textContent = message;
        }
    };

    button.addEventListener('click', async () => {
        const shareData = {
            title: button.dataset.shareTitle || document.title,
            url: window.location.href,
        };

        try {
            if (navigator.share) {
                await navigator.share(shareData);
                announce('Shared');
                return;
            }

            await copyCurrentUrl();
            announce('Link copied');
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            try {
                await copyCurrentUrl();
                announce('Link copied');
            } catch {
                announce('Unable to share');
            }
        }
    });
});

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

document.querySelectorAll('[data-vehicle-gallery]').forEach((gallery) => {
    const swiperElement = gallery.querySelector('[data-gallery-swiper]');
    const originalSlides = [...gallery.querySelectorAll('[data-gallery-slide]')];
    const thumbnails = [...gallery.querySelectorAll('[data-gallery-thumbnail]')];
    const previous = gallery.querySelector('[data-gallery-previous]');
    const next = gallery.querySelector('[data-gallery-next]');
    const lightbox = gallery.querySelector('[data-gallery-lightbox]');
    const lightboxImage = gallery.querySelector('[data-gallery-lightbox-image]');
    const lightboxClose = gallery.querySelector('[data-gallery-lightbox-close]');
    const lightboxPrevious = gallery.querySelector('[data-gallery-lightbox-previous]');
    const lightboxNext = gallery.querySelector('[data-gallery-lightbox-next]');
    const lightboxCount = gallery.querySelector('[data-gallery-lightbox-count]');

    if (!swiperElement || originalSlides.length === 0) {
        return;
    }

    const hasMultipleImages = originalSlides.length > 1;
    const initialIndex = Math.min(
        Number.parseInt(gallery.dataset.galleryInitialIndex || '0', 10),
        originalSlides.length - 1,
    );
    const autoplayDelay = Number.parseInt(gallery.dataset.galleryAutoplayDelay || '4500', 10);
    let lightboxIndex = initialIndex;
    let lightboxTransitionTimeout;
    let resumeAutoplayTimeout;
    let returnFocus = null;
    let pointerStartX = null;
    const isLightboxOpen = () => lightbox && !lightbox.hidden && lightbox.classList.contains('is-open');

    const imageAt = (index) => {
        const normalizedIndex = (index + originalSlides.length) % originalSlides.length;
        const slide = originalSlides[normalizedIndex];

        return {
            index: normalizedIndex,
            src: slide.dataset.galleryImage,
            srcset: slide.dataset.gallerySrcset,
            alt: slide.dataset.galleryAlt || '',
        };
    };

    const syncThumbnail = (index) => {
        thumbnails.forEach((thumbnail, thumbnailIndex) => {
            thumbnail.setAttribute('aria-pressed', String(thumbnailIndex === index));
        });

        const activeThumbnail = thumbnails[index];

        if (activeThumbnail && hasMultipleImages) {
            const thumbnailStrip = activeThumbnail.parentElement;
            const targetLeft = activeThumbnail.offsetLeft
                - ((thumbnailStrip.clientWidth - activeThumbnail.offsetWidth) / 2);

            thumbnailStrip.scrollTo({
                left: Math.max(0, targetLeft),
                behavior: reducedMotionMedia.matches ? 'auto' : 'smooth',
            });
        }
    };

    const swiper = new Swiper(swiperElement, {
        modules: [A11y, Autoplay, Keyboard, Navigation],
        initialSlide: initialIndex,
        loop: hasMultipleImages,
        speed: reducedMotionMedia.matches ? 0 : 700,
        grabCursor: hasMultipleImages,
        watchOverflow: true,
        keyboard: {
            enabled: hasMultipleImages,
            onlyInViewport: true,
        },
        navigation: hasMultipleImages
            ? {
                prevEl: previous,
                nextEl: next,
            }
            : false,
        autoplay: hasMultipleImages && !reducedMotionMedia.matches
            ? {
                delay: autoplayDelay,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            }
            : false,
        a11y: {
            enabled: true,
            prevSlideMessage: 'Previous vehicle photo',
            nextSlideMessage: 'Next vehicle photo',
            firstSlideMessage: 'This is the first vehicle photo',
            lastSlideMessage: 'This is the last vehicle photo',
        },
        on: {
            init(instance) {
                syncThumbnail(instance.realIndex);
            },
            realIndexChange(instance) {
                syncThumbnail(instance.realIndex);
            },
        },
    });

    const pauseThenResumeAutoplay = () => {
        if (!swiper.autoplay || reducedMotionMedia.matches) {
            return;
        }

        swiper.autoplay.stop();
        window.clearTimeout(resumeAutoplayTimeout);
        resumeAutoplayTimeout = window.setTimeout(() => {
            if (!isLightboxOpen() && !document.hidden) {
                swiper.autoplay.start();
            }
        }, 7000);
    };

    thumbnails.forEach((thumbnail, index) => {
        thumbnail.addEventListener('click', () => {
            swiper.slideToLoop(index);
            pauseThenResumeAutoplay();
        });
    });

    [previous, next].forEach((control) => {
        control?.addEventListener('click', pauseThenResumeAutoplay);
    });

    const updateLightbox = (index, animate = true) => {
        if (!lightboxImage) {
            return;
        }

        const image = imageAt(index);
        lightboxIndex = image.index;

        const applyImage = () => {
            lightboxImage.src = image.src;
            lightboxImage.alt = image.alt;

            if (image.srcset) {
                lightboxImage.srcset = image.srcset;
            } else {
                lightboxImage.removeAttribute('srcset');
            }

            if (lightboxCount) {
                lightboxCount.textContent = `${image.index + 1} / ${originalSlides.length}`;
            }

            swiper.slideToLoop(image.index);
            window.requestAnimationFrame(() => lightboxImage.classList.remove('is-changing'));
        };

        window.clearTimeout(lightboxTransitionTimeout);

        if (!animate || reducedMotionMedia.matches) {
            applyImage();
            return;
        }

        lightboxImage.classList.add('is-changing');
        lightboxTransitionTimeout = window.setTimeout(applyImage, 180);
    };

    const openLightbox = (trigger) => {
        if (!lightbox || !lightboxImage || isLightboxOpen()) {
            return;
        }

        returnFocus = trigger;
        updateLightbox(swiper.realIndex, false);
        swiper.autoplay?.stop();
        swiper.keyboard?.disable();
        lightboxImage.draggable = false;
        lightbox.hidden = false;
        lightbox.setAttribute('aria-hidden', 'false');
        lightbox.classList.add('is-open');
        document.body.classList.add('overflow-hidden');
        lightboxClose?.focus();
    };

    const closeLightbox = () => {
        if (!isLightboxOpen()) {
            return;
        }

        lightbox.classList.remove('is-open');
        lightbox.hidden = true;
        lightbox.setAttribute('aria-hidden', 'true');
        lightbox.dispatchEvent(new Event('close'));
    };

    const showPreviousLightboxImage = () => {
        updateLightbox(lightboxIndex - 1);
        pauseThenResumeAutoplay();
    };

    const showNextLightboxImage = () => {
        updateLightbox(lightboxIndex + 1);
        pauseThenResumeAutoplay();
    };

    swiperElement.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-gallery-lightbox-trigger]');

        if (trigger && swiper.allowClick) {
            openLightbox(trigger);
        }
    });

    lightboxClose?.addEventListener('click', closeLightbox);
    lightboxPrevious?.addEventListener('click', showPreviousLightboxImage);
    lightboxNext?.addEventListener('click', showNextLightboxImage);

    lightbox?.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeLightbox();
            return;
        }

        if (!hasMultipleImages) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            showPreviousLightboxImage();
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            showNextLightboxImage();
        }
    });

    lightbox?.querySelector('[data-gallery-lightbox-stage]')?.addEventListener('click', (event) => {
        if (event.target === event.currentTarget) {
            closeLightbox();
        }
    });

    lightboxImage?.addEventListener('pointerdown', (event) => {
        pointerStartX = event.clientX;
    });

    lightboxImage?.addEventListener('pointerup', (event) => {
        if (!hasMultipleImages || pointerStartX === null) {
            return;
        }

        const distance = event.clientX - pointerStartX;
        pointerStartX = null;

        if (Math.abs(distance) < 45) {
            return;
        }

        if (distance > 0) {
            showPreviousLightboxImage();
        } else {
            showNextLightboxImage();
        }
    });

    lightboxImage?.addEventListener('pointercancel', () => {
        pointerStartX = null;
    });

    lightbox?.addEventListener('close', () => {
        lightbox.hidden = true;
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        swiper.keyboard?.enable();

        if (swiper.autoplay && !reducedMotionMedia.matches && !document.hidden) {
            swiper.autoplay.start();
        }

        returnFocus?.focus();
        returnFocus = null;
    });

    document.addEventListener('visibilitychange', () => {
        if (!swiper.autoplay) {
            return;
        }

        if (document.hidden) {
            swiper.autoplay.stop();
        } else if (!isLightboxOpen()) {
            swiper.autoplay.start();
        }
    });
});

document.querySelectorAll('[data-request-modal]').forEach((modal) => {
    const triggers = document.querySelectorAll(`[data-request-modal-trigger="${modal.id}"]`);
    const closeButtons = modal.querySelectorAll('[data-request-modal-close]');
    let returnFocus = null;

    const openModal = (trigger = null) => {
        returnFocus = trigger;

        if (typeof modal.showModal === 'function') {
            modal.showModal();
        } else {
            modal.setAttribute('open', '');
        }
    };

    const closeModal = () => {
        if (typeof modal.close === 'function') {
            modal.close();
        } else {
            modal.removeAttribute('open');
            returnFocus?.focus();
        }
    };

    triggers.forEach((trigger) => trigger.addEventListener('click', () => openModal(trigger)));
    closeButtons.forEach((button) => button.addEventListener('click', closeModal));

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    modal.addEventListener('close', () => returnFocus?.focus());

    if (modal.dataset.openOnLoad === 'true') {
        window.requestAnimationFrame(() => openModal(triggers[0] ?? null));
    }

    modal.querySelector('[data-request-form]')?.addEventListener('submit', (event) => {
        event.currentTarget.setAttribute('aria-busy', 'true');
        const submitButton = event.currentTarget.querySelector('[data-request-submit]');

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Sending…';
        }
    });
});
