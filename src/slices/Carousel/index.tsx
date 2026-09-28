"use client";

import { useLocale } from "@/i18n/LocaleProvider";
import StudioLighting from "@/components/StudioLighting";

import { Content } from "@prismicio/client";
import {
  PrismicRichText,
  PrismicText,
  SliceComponentProps,
} from "@prismicio/react";
import { Center, View } from "@react-three/drei";
import { useRef, useState } from "react";
import clsx from "clsx";
import { Group } from "three";
import gsap from "gsap";

import FloatingCan from "@/components/FloatingCan";
import { SodaCanProps } from "@/components/SodaCan";
import { ArrowIcon } from "./ArrowIcon";
import { WavyCircles } from "./WavyCircles";

const SPINS_ON_CHANGE = 1;
const FLAVORS: {
  flavor: SodaCanProps["flavor"];
  color: string;
  name: string;
  slug: string;
  shortName: string;
}[] = [
  {
    flavor: "blackCherry",
    color: "#34543B",
    name: "Antep Fıstığı Ezmesi",
    slug: "antep-fistigi",
    shortName: "Antep fıstığı",
  },
  {
    flavor: "grape",
    color: "#704737",
    name: "Fındık Ezmesi",
    slug: "findik",
    shortName: "Fındık",
  },
  {
    flavor: "lemonLime",
    color: "#415E59",
    name: "Hindistan Cevizi Ezmesi",
    slug: "hindistan-cevizi",
    shortName: "Hindistan cevizi",
  },
  {
    flavor: "strawberryLemonade",
    color: "#895845",
    name: "Badem Ezmesi",
    slug: "badem",
    shortName: "Badem",
  },
  {
    flavor: "watermelon",
    color: "#856027",
    name: "Yer Fıstığı Ezmesi",
    slug: "yer-fistigi",
    shortName: "Yer fıstığı",
  },
];

/**
 * Props for `Carousel`.
 */
export type CarouselProps = SliceComponentProps<Content.CarouselSlice>;

/**
 * Component for "Carousel" Slices.
 */
const Carousel = ({ slice }: CarouselProps): JSX.Element => {
  const { t } = useLocale();
  const [currentFlavorIndex, setCurrentFlavorIndex] = useState(0);
  const sodaCanRef = useRef<Group>(null);
  const transitioning = useRef(false);

  function changeFlavor(index: number) {
    if (transitioning.current) return;
    const nextIndex = (index + FLAVORS.length) % FLAVORS.length;
    if (nextIndex === currentFlavorIndex) return;
    if (!sodaCanRef.current) {
      setCurrentFlavorIndex(nextIndex);
      return;
    }
    transitioning.current = true;

    const tl = gsap.timeline({
      onComplete: () => {
        transitioning.current = false;
      },
    });

    tl.to(
      sodaCanRef.current.rotation,
      {
        y:
          index > currentFlavorIndex
            ? `-=${Math.PI * 2 * SPINS_ON_CHANGE}`
            : `+=${Math.PI * 2 * SPINS_ON_CHANGE}`,
        ease: "power2.inOut",
        duration: window.matchMedia("(prefers-reduced-motion: reduce)").matches
          ? 0
          : 1,
      },
      0,
    )
      .to(
        ".background, .wavy-circles-outer, .wavy-circles-inner",
        {
          backgroundColor: FLAVORS[nextIndex].color,
          fill: FLAVORS[nextIndex].color,
          ease: "power2.inOut",
          duration: 1,
        },
        0,
      )
      .to(".text-wrapper", { duration: 0.2, y: -10, opacity: 0 }, 0)
      .to({}, { onStart: () => setCurrentFlavorIndex(nextIndex) }, 0.5)
      .to(".text-wrapper", { duration: 0.2, y: 0, opacity: 1 }, 0.7);
  }

  return (
    <section
      id="lezzetler"
      aria-label={t.selectFlavor}
      tabIndex={0}
      onKeyDown={(event) => {
        if (event.key === "ArrowRight" || event.key === "ArrowLeft") {
          event.preventDefault();
          changeFlavor(
            currentFlavorIndex + (event.key === "ArrowRight" ? 1 : -1),
          );
        }
      }}
      data-slice-type={slice.slice_type}
      data-slice-variation={slice.variation}
      className="carousel relative grid h-screen grid-rows-[auto,4fr,auto] justify-center overflow-hidden bg-white py-12 text-white"
    >
      <div
        style={{ backgroundColor: FLAVORS[currentFlavorIndex].color }}
        className="background pointer-events-none absolute inset-0 bg-[#34543B] opacity-100"
      />

      <WavyCircles className="carousel-waves absolute left-1/2 top-1/2 h-[120vmin] -translate-x-1/2 -translate-y-1/2 text-[#34543B]" />

      <h2 className="relative text-center text-5xl font-bold">
        <span className="eyebrow block">{t.carouselEyebrow}</span>
        <PrismicText field={slice.primary.heading} />
      </h2>

      <div className="grid grid-cols-[auto,auto,auto] items-center">
        {/* Left */}
        <ArrowButton
          onClick={() => changeFlavor(currentFlavorIndex - 1)}
          direction="left"
          label={t.previous}
        />
        {/* Can */}
        <View className="aspect-square h-[70vmin] min-h-40">
          <Center position={[0, 0, 1.5]}>
            <FloatingCan
              ref={sodaCanRef}
              floatIntensity={0.3}
              rotationIntensity={1}
              flavor={FLAVORS[currentFlavorIndex].flavor}
            />
          </Center>
          <StudioLighting />
        </View>
        {/* Right */}
        <ArrowButton
          onClick={() => changeFlavor(currentFlavorIndex + 1)}
          direction="right"
          label={t.next}
        />
      </div>

      <div className="text-area relative mx-auto text-center">
        <div className="text-wrapper text-4xl font-medium" aria-live="polite">
          <span className="flavor-number">0{currentFlavorIndex + 1} / 05</span>
          <p>{t.productNames[currentFlavorIndex]}</p>
        </div>
        <div className="mt-2 text-2xl font-normal opacity-90">
          <PrismicRichText field={slice.primary.price_copy} />
        </div>
        <div
          className="flavor-options"
          role="group"
          aria-label={t.directSelection}
        >
          {FLAVORS.map((item, index) => (
            <button
              type="button"
              key={item.slug}
              aria-pressed={index === currentFlavorIndex}
              onClick={() => changeFlavor(index)}
            >
              {t.shortNames[index]}
            </button>
          ))}
        </div>
        <a
          className="product-detail-link"
          href={"#urun-" + FLAVORS[currentFlavorIndex].slug}
        >
          {t.discoverProduct}
        </a>
      </div>
    </section>
  );
};

export default Carousel;

type ArrowButtonProps = {
  direction?: "right" | "left";
  label: string;
  onClick: () => void;
};

function ArrowButton({
  label,
  onClick,
  direction = "right",
}: ArrowButtonProps) {
  return (
    <button
      onClick={onClick}
      className="size-12 rounded-full border-2 border-white bg-white/10 p-3 opacity-85 ring-white focus:outline-none focus-visible:opacity-100 focus-visible:ring-4 md:size-16 lg:size-20"
    >
      <ArrowIcon className={clsx(direction === "right" && "-scale-x-100")} />
      <span className="sr-only">{label}</span>
    </button>
  );
}
