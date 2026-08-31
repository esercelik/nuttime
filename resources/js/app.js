import Alpine from 'alpinejs';
import { gsap } from 'gsap';

window.Alpine = Alpine;
Alpine.start();

const initializeProductHeroes = () => {
    document.querySelectorAll('[data-product-hero]').forEach((hero) => {
        const slides = Array.from(hero.querySelectorAll('[data-product-hero-slide]'));

        if (slides.length === 0) {
            return;
        }

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const desktopPointer = window.matchMedia('(min-width: 901px) and (pointer: fine)').matches;
        const autoplayDelay = Number(hero.dataset.autoplay) || 6500;
        const previousButton = hero.querySelector('[data-product-hero-previous]');
        const nextButton = hero.querySelector('[data-product-hero-next]');
        const currentLabel = hero.querySelector('[data-product-hero-current]');
        const progressIndicator = hero.querySelector('[data-product-hero-progress]');
        const paginationButtons = Array.from(hero.querySelectorAll('[data-product-hero-pagination]'));
        let activeIndex = 0;
        let autoplayTimer;
        let activeTimeline;
        let activeFloat;
        let activeAmbient;
        let activeProgress;
        let transitionTween;
        let pointerStart;
        let parallaxSetters;
        let isTransitioning = false;

        const animationContext = gsap.context(() => {}, hero);
        const getScene = (slide) => ({
            background: slide.querySelector('[data-product-hero-background]'),
            eyebrow: slide.querySelector('.product-hero__eyebrow'),
            headlineLines: slide.querySelectorAll('[data-product-hero-headline]'),
            description: slide.querySelector('.product-hero__description'),
            actions: slide.querySelector('[data-product-hero-actions]'),
            jar: slide.querySelector('[data-product-hero-jar]'),
            jarFloat: slide.querySelector('[data-product-hero-jar-float]'),
            visual: slide.querySelector('[data-product-hero-visual]'),
            backdropType: slide.querySelector('.product-hero__backdrop-type'),
            decorations: slide.querySelectorAll('[data-product-hero-decoration]'),
        });

        const updateControls = () => {
            paginationButtons.forEach((button, index) => {
                const isActive = index === activeIndex;

                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-current', isActive ? 'true' : 'false');
            });

            if (currentLabel) {
                currentLabel.textContent = String(activeIndex + 1).padStart(2, '0');
            }
        };

        const stopAutoplay = () => {
            window.clearTimeout(autoplayTimer);
            activeProgress?.kill();

            if (progressIndicator) {
                gsap.set(progressIndicator, { scaleX: 0 });
            }
        };

        const startAutoplay = () => {
            if (reduceMotion || slides.length < 2 || document.hidden) {
                return;
            }

            stopAutoplay();
            activeProgress = progressIndicator
                ? gsap.fromTo(progressIndicator, { scaleX: 0 }, { scaleX: 1, duration: autoplayDelay / 1000, ease: 'none' })
                : undefined;
            autoplayTimer = window.setTimeout(() => {
                activateSlide(activeIndex + 1);
            }, autoplayDelay);
        };

        const setSlideInteractivity = (slide, isActive) => {
            slide.querySelectorAll('a, button').forEach((element) => {
                element.tabIndex = isActive ? 0 : -1;
            });
        };

        const prepareParallax = (slide) => {
            if (!desktopPointer) {
                return;
            }

            const scene = getScene(slide);
            parallaxSetters = {
                backgroundX: gsap.quickTo(scene.background, 'x', { duration: 0.8, ease: 'power3.out' }),
                backgroundY: gsap.quickTo(scene.background, 'y', { duration: 0.8, ease: 'power3.out' }),
                visualX: gsap.quickTo(scene.visual, 'x', { duration: 0.7, ease: 'power3.out' }),
                visualY: gsap.quickTo(scene.visual, 'y', { duration: 0.7, ease: 'power3.out' }),
                typeX: gsap.quickTo(scene.backdropType, 'x', { duration: 0.9, ease: 'power3.out' }),
            };
        };

        const playSlideEntrance = (slide) => {
            if (reduceMotion) {
                return;
            }

            const scene = getScene(slide);

            activeTimeline?.kill();
            activeFloat?.kill();
            activeAmbient?.kill();
            animationContext.add(() => {
                gsap.set([
                    scene.background,
                    scene.eyebrow,
                    scene.headlineLines,
                    scene.description,
                    scene.actions,
                    scene.jar,
                    scene.backdropType,
                    scene.decorations,
                ], { clearProps: 'all' });

                activeTimeline = gsap.timeline({ defaults: { ease: 'power3.out' } })
                    .fromTo(scene.background, { autoAlpha: 0, scale: 1.1, clipPath: 'inset(0 0 0 100%)' }, { autoAlpha: 1, scale: 1, clipPath: 'inset(0 0 0 0%)', duration: 1.25 })
                    .from(scene.backdropType, { autoAlpha: 0, x: 100, duration: 0.8 }, '-=0.9')
                    .from(scene.decorations, { autoAlpha: 0, x: 45, y: 25, rotation: 18, stagger: 0.07, duration: 0.64 }, '-=0.72')
                    .from(scene.eyebrow, { autoAlpha: 0, x: -24, duration: 0.45 }, '-=0.75')
                    .from(scene.headlineLines, { autoAlpha: 0, yPercent: 120, stagger: 0.11, duration: 0.72 }, '-=0.16')
                    .from(scene.description, { autoAlpha: 0, y: 22, duration: 0.46 }, '-=0.36')
                    .from(scene.jar, { autoAlpha: 0, x: 90, y: 70, rotation: -7, scale: 0.87, duration: 1.05, ease: 'back.out(1.2)' }, '-=0.95')
                    .from(scene.actions, { autoAlpha: 0, y: 18, duration: 0.42 }, '-=0.48');

                activeFloat = gsap.to(scene.jarFloat, {
                    y: -12,
                    rotation: -1.2,
                    duration: 3.4,
                    ease: 'sine.inOut',
                    yoyo: true,
                    repeat: -1,
                });
                activeAmbient = gsap.to(scene.decorations, {
                    y: '+=14',
                    rotation: '+=8',
                    duration: 4.7,
                    ease: 'sine.inOut',
                    stagger: 0.2,
                    yoyo: true,
                    repeat: -1,
                });
            });

            prepareParallax(slide);
        };

        const activateSlide = (nextIndex) => {
            const normalizedIndex = (nextIndex + slides.length) % slides.length;

            if (normalizedIndex === activeIndex) {
                startAutoplay();

                return;
            }

            if (isTransitioning) {
                return;
            }

            const previousSlide = slides[activeIndex];
            const nextSlide = slides[normalizedIndex];
            const previousScene = getScene(previousSlide);

            stopAutoplay();
            isTransitioning = true;
            transitionTween?.kill();
            nextSlide.hidden = false;
            nextSlide.classList.add('is-active');
            nextSlide.setAttribute('aria-hidden', 'false');
            setSlideInteractivity(nextSlide, true);
            previousSlide.classList.remove('is-active');
            previousSlide.setAttribute('aria-hidden', 'true');
            setSlideInteractivity(previousSlide, false);
            activeIndex = normalizedIndex;
            updateControls();

            if (reduceMotion) {
                previousSlide.hidden = true;
                isTransitioning = false;
            } else {
                gsap.set(nextSlide, { autoAlpha: 1 });
                transitionTween = gsap.timeline({
                    onComplete: () => {
                        previousSlide.hidden = true;
                        gsap.set(previousSlide, { clearProps: 'opacity,visibility' });
                        isTransitioning = false;
                    },
                });
                transitionTween
                    .to(previousScene.backdropType, { autoAlpha: 0, y: -45, duration: 0.42, ease: 'power2.in' }, 0)
                    .to([previousScene.eyebrow, previousScene.headlineLines, previousScene.description, previousScene.actions], { autoAlpha: 0, y: -34, stagger: 0.035, duration: 0.38, ease: 'power2.in' }, 0)
                    .to(previousScene.jar, { autoAlpha: 0, x: -70, y: -35, rotation: 5, scale: 1.08, duration: 0.68, ease: 'power2.inOut' }, 0)
                    .to(previousScene.decorations, { autoAlpha: 0, x: -55, y: -40, rotation: -16, stagger: 0.05, duration: 0.56, ease: 'power2.in' }, 0)
                    .to(previousSlide, { autoAlpha: 0, duration: 0.68, ease: 'power2.out' }, 0.08);
                playSlideEntrance(nextSlide);
            }

            startAutoplay();
        };

        const onPrevious = () => activateSlide(activeIndex - 1);
        const onNext = () => activateSlide(activeIndex + 1);
        const onKeydown = (event) => {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                onPrevious();
            }

            if (event.key === 'ArrowRight') {
                event.preventDefault();
                onNext();
            }
        };
        const onPointerDown = (event) => {
            pointerStart = { x: event.clientX, y: event.clientY };
            stopAutoplay();
        };
        const onPointerUp = (event) => {
            if (!pointerStart) {
                return;
            }

            const deltaX = event.clientX - pointerStart.x;
            const deltaY = event.clientY - pointerStart.y;
            pointerStart = undefined;

            if (Math.abs(deltaX) > 44 && Math.abs(deltaX) > Math.abs(deltaY)) {
                activateSlide(activeIndex + (deltaX < 0 ? 1 : -1));

                return;
            }

            startAutoplay();
        };
        const onPointerCancel = () => {
            pointerStart = undefined;
            startAutoplay();
        };
        const onFocusOut = () => {
            window.requestAnimationFrame(() => {
                if (!hero.contains(document.activeElement)) {
                    startAutoplay();
                }
            });
        };
        const onPointerMove = (event) => {
            if (!parallaxSetters) {
                return;
            }

            const bounds = hero.getBoundingClientRect();
            const pointerX = (event.clientX - bounds.left) / bounds.width - 0.5;
            const pointerY = (event.clientY - bounds.top) / bounds.height - 0.5;

            parallaxSetters.backgroundX(pointerX * -12);
            parallaxSetters.backgroundY(pointerY * -8);
            parallaxSetters.visualX(pointerX * 16);
            parallaxSetters.visualY(pointerY * 10);
            parallaxSetters.typeX(pointerX * 12);
        };
        const onPointerLeave = () => {
            if (!parallaxSetters) {
                return;
            }

            Object.values(parallaxSetters).forEach((setter) => setter(0));
        };
        const onVisibilityChange = () => {
            if (document.hidden) {
                stopAutoplay();

                return;
            }

            startAutoplay();
        };

        previousButton?.addEventListener('click', onPrevious);
        nextButton?.addEventListener('click', onNext);
        const paginationHandlers = paginationButtons.map((button) => {
            const onPaginationClick = () => activateSlide(Number(button.dataset.productHeroPagination));

            button.addEventListener('click', onPaginationClick);

            return [button, onPaginationClick];
        });
        hero.addEventListener('keydown', onKeydown);
        hero.addEventListener('pointerdown', onPointerDown);
        hero.addEventListener('pointerup', onPointerUp);
        hero.addEventListener('pointercancel', onPointerCancel);
        hero.addEventListener('mouseenter', stopAutoplay);
        hero.addEventListener('mouseleave', startAutoplay);
        hero.addEventListener('focusin', stopAutoplay);
        hero.addEventListener('focusout', onFocusOut);
        hero.addEventListener('pointermove', onPointerMove);
        hero.addEventListener('pointerleave', onPointerLeave);
        document.addEventListener('visibilitychange', onVisibilityChange);

        slides.forEach((slide, index) => setSlideInteractivity(slide, index === activeIndex));

        const removeInteractionListeners = () => {
            previousButton?.removeEventListener('click', onPrevious);
            nextButton?.removeEventListener('click', onNext);
            paginationHandlers.forEach(([button, handler]) => button.removeEventListener('click', handler));
            hero.removeEventListener('keydown', onKeydown);
            hero.removeEventListener('pointerdown', onPointerDown);
            hero.removeEventListener('pointerup', onPointerUp);
            hero.removeEventListener('pointercancel', onPointerCancel);
            hero.removeEventListener('mouseenter', stopAutoplay);
            hero.removeEventListener('mouseleave', startAutoplay);
            hero.removeEventListener('focusin', stopAutoplay);
            hero.removeEventListener('focusout', onFocusOut);
            hero.removeEventListener('pointermove', onPointerMove);
            hero.removeEventListener('pointerleave', onPointerLeave);
            document.removeEventListener('visibilitychange', onVisibilityChange);
        };

        if (reduceMotion) {
            window.addEventListener('pagehide', removeInteractionListeners, { once: true });

            return;
        }

        playSlideEntrance(slides[activeIndex]);
        startAutoplay();

        const teardown = () => {
            animationContext.revert();
            removeInteractionListeners();
            stopAutoplay();
            activeTimeline?.kill();
            activeFloat?.kill();
            activeAmbient?.kill();
            transitionTween?.kill();
        };

        window.addEventListener('pagehide', teardown, { once: true });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeProductHeroes, { once: true });
} else {
    initializeProductHeroes();
}
