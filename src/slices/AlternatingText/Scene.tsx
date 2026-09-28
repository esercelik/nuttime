"use client";

import StudioLighting from "@/components/StudioLighting";

import { useRef } from "react";
import { Group } from "three";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useGSAP } from "@gsap/react";

import FloatingCan from "@/components/FloatingCan";
import { NuttimeSpoon } from "@/components/SodaCan";
import { useMediaQuery } from "@/hooks/useMediaQuery";

gsap.registerPlugin(ScrollTrigger, useGSAP);

type Props = {};

export default function Scene({}: Props) {
  const canRef = useRef<Group>(null);
  const spoonRef = useRef<Group>(null);
  const isDesktop = useMediaQuery("(min-width: 768px)", true);

  const reducedMotion = useMediaQuery(
    "(prefers-reduced-motion: reduce)",
    false,
  );

  const bgColors = ["#E5E9D4", "#EFE5D5", "#D7E2C5"];

  useGSAP(
    () => {
      if (!canRef.current || !spoonRef.current) return;

      const spoon = spoonRef.current;
      const lid = canRef.current.getObjectByName("NuttimeLid");
      const spoonful = spoon.getObjectByName("Spoonful");
      if (reducedMotion) {
        gsap.set(spoon.position, { x: -0.1, y: 0.74, z: 0.32 });
        gsap.set(spoon.rotation, { x: 0.35, y: -0.15, z: 0.25 });
        if (lid) gsap.set(lid.position, { x: -0.065, y: 0.12 });
        return;
      }
      gsap.set(spoon.position, { x: 0.65, y: 1.5, z: -0.15 });
      gsap.set(spoon.rotation, { x: 0.12, y: -0.3, z: 1.1 });
      if (spoonful) gsap.set(spoonful.scale, { x: 0.01, y: 0.01, z: 0.01 });
      const sections = gsap.utils.toArray(".alternating-section");

      const scrollTl = gsap.timeline({
        scrollTrigger: {
          trigger: ".alternating-text-view",
          endTrigger: ".alternating-text-container",
          pin: true,
          start: "top top",
          end: "bottom bottom",
          scrub: 0.65,
          invalidateOnRefresh: true,
        },
      });

      if (lid) {
        scrollTl.to(
          lid.rotation,
          { y: -Math.PI * 1.2, duration: 0.32, ease: "power2.inOut" },
          0,
        );
        scrollTl.to(
          lid.position,
          { y: 0.1, duration: 0.32, ease: "power2.inOut" },
          0,
        );
        scrollTl.to(
          lid.position,
          {
            y: 0.125,
            x: -0.065,
            z: -0.025,
            duration: 0.35,
            ease: "power2.inOut",
          },
          0.25,
        );
        scrollTl.to(
          lid.rotation,
          { z: -0.55, x: -0.2, duration: 0.35, ease: "power2.inOut" },
          0.25,
        );
      }
      scrollTl
        .to(
          spoon.position,
          { x: 0.04, y: 0.62, z: 0.04, duration: 0.35, ease: "power2.out" },
          0.25,
        )
        .to(
          spoon.rotation,
          { x: 0.15, y: -0.25, z: 0.7, duration: 0.35, ease: "power2.inOut" },
          0.25,
        )
        .to(
          spoon.position,
          { x: -0.08, y: 0.3, z: 0.05, duration: 0.25, ease: "power2.inOut" },
          0.6,
        )
        .to(
          spoon.rotation,
          { z: 0.32, duration: 0.25, ease: "power2.inOut" },
          0.6,
        )
        .to(
          spoon.position,
          { x: -0.18, y: 0.34, z: 0.16, duration: 0.22, ease: "sine.inOut" },
          0.85,
        )
        .to(
          spoon.position,
          { x: -0.24, y: 0.86, z: 0.38, duration: 0.42, ease: "power2.inOut" },
          1.07,
        )
        .to(
          spoon.rotation,
          { x: 0.5, y: -0.2, z: 0.18, duration: 0.42, ease: "power2.inOut" },
          1.07,
        )
        .to(
          spoon.position,
          { x: -0.32, y: 0.94, z: 0.56, duration: 0.51, ease: "sine.inOut" },
          1.49,
        )
        .to(
          spoon.rotation,
          { x: 0.65, y: 0.1, z: 0.28, duration: 0.51, ease: "sine.inOut" },
          1.49,
        );
      if (spoonful)
        scrollTl.to(
          spoonful.scale,
          { x: 1, y: 1, z: 1, duration: 0.23, ease: "sine.inOut" },
          0.85,
        );

      sections.forEach((_, index) => {
        if (
          !canRef.current ||
          window.matchMedia("(prefers-reduced-motion: reduce)").matches
        )
          return;
        if (index === 0) return;

        const isOdd = index % 2 !== 0;

        const xPosition = isDesktop ? (isOdd ? "-1" : "1") : 0;
        const yRotation = isDesktop ? (isOdd ? ".4" : "-.4") : 0;
        scrollTl
          .to(
            canRef.current.position,
            {
              x: xPosition,
              ease: "circ.inOut",
              duration: 0.5,
            },
            index - 0.5,
          )
          .to(
            canRef.current.rotation,
            {
              y: yRotation,
              ease: "power2.inOut",
              duration: 0.5,
            },
            index - 0.5,
          )
          .to(
            ".alternating-text-container",
            {
              backgroundColor: gsap.utils.wrap(bgColors, index),
              duration: 0.5,
            },
            index - 0.5,
          );
      });
    },
    { dependencies: [isDesktop, reducedMotion], revertOnUpdate: true },
  );

  return (
    <>
      <StudioLighting />
      <group
        ref={canRef}
        scale={isDesktop ? 0.95 : 0.62}
        position-y={isDesktop ? -0.08 : 0.35}
        position-x={isDesktop ? 1 : 0}
        rotation-y={isDesktop ? -0.3 : 0}
      >
        <FloatingCan
          flavor="lemonLime"
          rotationIntensity={0}
          floatIntensity={0}
        />
        <group ref={spoonRef}>
          <NuttimeSpoon />
        </group>
      </group>
    </>
  );
}
