import Alpine from 'alpinejs';
import { gsap } from 'gsap';

window.Alpine = Alpine;
Alpine.start();

const initializeProductHeroes = () => {
    document.querySelectorAll('[data-product-hero]').forEach((hero) => {
        const slides = Array.from(hero.querySelectorAll('[data-product-hero-slide]'));

        if (slides.length < 2) {
            return;
        }

        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const autoplayDelay = Number(hero.dataset.autoplay) || 6500;
        const previousButton = hero.querySelector('[data-product-hero-previous]');
        const nextButton = hero.querySelector('[data-product-hero-next]');
        const currentLabel = hero.querySelector('[data-product-hero-current]');
        const paginationButtons = Array.from(hero.querySelectorAll('[data-product-hero-pagination]'));
        let activeIndex = 0;
        let autoplayTimer;
        let activeTimeline;
        let activeFloat;
        let transitionTween;
        let pointerStart;

        const animationContext = gsap.context(() => {}, hero);
        const getScene = (slide) => ({
            background: slide.querySelector('[data-product-hero-background]'),
            eyebrow: slide.querySelector('.product-hero__eyebrow'),
            headlineLines: slide.querySelectorAll('[data-product-hero-headline]'),
            description: slide.querySelector('.product-hero__description'),
            actions: slide.querySelector('[data-product-hero-actions]'),
            jar: slide.querySelector('[data-product-hero-jar]'),
            jarFloat: slide.querySelector('[data-product-hero-jar-float]'),
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
        };

        const startAutoplay = () => {
            if (reduceMotion) {
                return;
            }

            stopAutoplay();
            autoplayTimer = window.setTimeout(() => {
                activateSlide(activeIndex + 1);
            }, autoplayDelay);
        };

        const playSlideEntrance = (slide) => {
            if (reduceMotion) {
                return;
            }

            const scene = getScene(slide);

            activeTimeline?.kill();
            activeFloat?.kill();
            animationContext.add(() => {
                gsap.set([
                    scene.background,
                    scene.eyebrow,
                    scene.headlineLines,
                    scene.description,
                    scene.actions,
                    scene.jar,
                ], { clearProps: 'all' });

                activeTimeline = gsap.timeline({ defaults: { ease: 'power3.out' } })
                    .fromTo(scene.background, { autoAlpha: 0, scale: 1.08 }, { autoAlpha: 1, scale: 1, duration: 1.2 })
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
            });
        };

        const activateSlide = (nextIndex) => {
            const normalizedIndex = (nextIndex + slides.length) % slides.length;

            if (normalizedIndex === activeIndex) {
                startAutoplay();

                return;
            }

            const previousSlide = slides[activeIndex];
            const nextSlide = slides[normalizedIndex];

            stopAutoplay();
            transitionTween?.kill();
            nextSlide.hidden = false;
            nextSlide.classList.add('is-active');
            nextSlide.setAttribute('aria-hidden', 'false');
            previousSlide.classList.remove('is-active');
            previousSlide.setAttribute('aria-hidden', 'true');
            activeIndex = normalizedIndex;
            updateControls();

            if (reduceMotion) {
                previousSlide.hidden = true;
            } else {
                gsap.set(nextSlide, { autoAlpha: 1 });
                transitionTween = gsap.to(previousSlide, {
                    autoAlpha: 0,
                    duration: 0.32,
                    ease: 'power2.out',
                    onComplete: () => {
                        previousSlide.hidden = true;
                        gsap.set(previousSlide, { clearProps: 'opacity,visibility' });
                    },
                });
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
