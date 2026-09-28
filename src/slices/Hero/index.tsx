"use client";

import { useLocale } from "@/i18n/LocaleProvider";
import StudioLighting from "@/components/StudioLighting";

import { asText, Content } from "@prismicio/client";
import Image from "next/image";
import { PrismicRichText, SliceComponentProps } from "@prismicio/react";
import gsap from "gsap";
import { useGSAP } from "@gsap/react";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { View } from "@react-three/drei";
import FloatingCan from "@/components/FloatingCan";

import { Bounded } from "@/components/Bounded";
import Button from "@/components/Button";
import { TextSplitter } from "@/components/TextSplitter";
import Scene from "./Scene";
import { Bubbles } from "./Bubbles";
import { useStore } from "@/hooks/useStore";
import { useMediaQuery } from "@/hooks/useMediaQuery";

gsap.registerPlugin(useGSAP, ScrollTrigger);

/**
 * Props for `Hero`.
 */
export type HeroProps = SliceComponentProps<Content.HeroSlice>;

/**
 * Component for "Hero" Slices.
 */
const Hero = ({ slice }: HeroProps): JSX.Element => {
  const { t } = useLocale();
  const reducedMotion = useMediaQuery(
    "(prefers-reduced-motion: reduce)",
    false,
  );
  const ready = useStore((state) => state.ready);
  const isDesktop = useMediaQuery("(min-width: 768px)", true);

  useGSAP(
    () => {
      if (!ready && isDesktop) return;

      if (reducedMotion) {
        gsap.set(".hero", { opacity: 1 });
        return;
      }
      const introTl = gsap.timeline();

      introTl
        .set(".hero", { opacity: 1 })
        .from(".hero-header-word", {
          scale: 1.1,
          opacity: 0,
          ease: "power4.in",
          delay: 0.3,
          stagger: 0.15,
        })
        .from(
          ".hero-subheading",
          {
            opacity: 0,
            y: 30,
          },
          "<0.1",
        )
        .from(".hero-body", {
          opacity: 0,
          y: 10,
        })
        .from(".hero-button", {
          opacity: 0,
          y: 10,
          duration: 0.6,
        });

      const scrollTl = gsap.timeline({
        scrollTrigger: {
          trigger: ".hero",
          start: "top top",
          end: "bottom bottom",
          scrub: 1.5,
        },
      });

      scrollTl
        .fromTo(
          "body",
          {
            backgroundColor: "#F4F0E4",
          },
          {
            backgroundColor: "#DEE6C7",
            overwrite: "auto",
          },
          1,
        )
        .from(".text-side-heading .split-char", {
          scale: 1.3,
          y: 40,
          rotate: -25,
          opacity: 0,
          stagger: 0.1,
          ease: "back.out(3)",
          duration: 0.5,
        })
        .from(".text-side-body", {
          y: 20,
          opacity: 0,
        });
    },
    { dependencies: [ready, isDesktop, reducedMotion], revertOnUpdate: true },
  );

  return (
    <Bounded
      data-slice-type={slice.slice_type}
      data-slice-variation={slice.variation}
      className="hero"
    >
      {isDesktop && (
        <View className="hero-scene pointer-events-none sticky top-0 z-50 -mt-[100vh] hidden h-screen w-screen md:block">
          <Scene />
        </View>
      )}

      <div className="grid">
        <div className="grid h-screen place-items-center">
          <div className="hero-composition grid auto-rows-min place-items-center text-center">
            <p className="eyebrow hero-eyebrow">{t.heroEyebrow}</p>
            <h1
              aria-label={asText(slice.primary.heading)}
              className="hero-header text-7xl font-black uppercase leading-[.8] text-[#31543A] md:text-[8rem] lg:text-[10rem]"
            >
              <TextSplitter
                text={asText(slice.primary.heading)}
                wordDisplayStyle="block"
                className="hero-header-word"
              />
            </h1>
            {!isDesktop && (
              <View className="mobile-hero-view h-[270px] w-screen">
                <group position={[-0.7, 0, 0]} rotation={[0, 0.08, -0.2]}>
                  <FloatingCan flavor="blackCherry" rotationIntensity={0.15} />
                </group>
                <group position={[0.7, 0, 0.1]} rotation={[0, -0.08, 0.2]}>
                  <FloatingCan flavor="lemonLime" rotationIntensity={0.15} />
                </group>
                <StudioLighting />
              </View>
            )}
            <div className="hero-subheading mt-12 text-5xl font-semibold text-sky-950 lg:text-6xl">
              <PrismicRichText field={slice.primary.subheading} />
            </div>
            <div className="hero-body text-2xl font-normal text-sky-950">
              <PrismicRichText field={slice.primary.body} />
            </div>
            <Button
              buttonLink={slice.primary.button_link}
              buttonText={slice.primary.button_text}
              className="hero-button mt-12"
            />
            <a className="scroll-cue" href="#lezzetler">
              <span aria-hidden="true">↓</span>
              {t.scrollHint}
            </a>
          </div>
        </div>

        <div className="text-side relative z-[80] grid h-screen items-center gap-4 md:grid-cols-2">
          <Image
            className="w-full md:hidden"
            src="/nuttime/coconut.png"
            alt={t.productNames[2]}
            width={1707}
            height={2560}
            sizes="100vw"
          />
          <div>
            <p className="eyebrow">{t.beyond}</p>
            <h2
              aria-label={asText(slice.primary.second_heading)}
              className="text-side-heading text-balance text-6xl font-black uppercase text-sky-950 lg:text-8xl"
            >
              <TextSplitter text={asText(slice.primary.second_heading)} />
            </h2>
            <div className="text-side-body mt-4 max-w-xl text-balance text-xl font-normal text-sky-950">
              <PrismicRichText field={slice.primary.second_body} />
            </div>
          </div>
        </div>
      </div>
    </Bounded>
  );
};

export default Hero;
