"use client";

import { forwardRef, ReactNode } from "react";
import { Float } from "@react-three/drei";

import { SodaCan, SodaCanProps } from "@/components/SodaCan";
import { Group } from "three";
import { useMediaQuery } from "@/hooks/useMediaQuery";

type FloatingCanProps = {
  flavor?: SodaCanProps["flavor"];
  floatSpeed?: number;
  rotationIntensity?: number;
  floatIntensity?: number;
  floatingRange?: [number, number];
  children?: ReactNode;
};

const FloatingCan = forwardRef<Group, FloatingCanProps>(
  (
    {
      flavor = "blackCherry",
      floatSpeed = 1.5,
      rotationIntensity = 1,
      floatIntensity = 1,
      floatingRange = [-0.1, 0.1],
      children,
      ...props
    },
    ref,
  ) => {
    const reducedMotion = useMediaQuery(
      "(prefers-reduced-motion: reduce)",
      false,
    );
    return (
      <group ref={ref} {...props}>
        {rotationIntensity === 0 && floatIntensity === 0 ? (
          <>
            {children}
            <SodaCan flavor={flavor} />
          </>
        ) : (
          <Float
            speed={reducedMotion ? 0 : floatSpeed}
            rotationIntensity={rotationIntensity}
            floatIntensity={floatIntensity}
            floatingRange={floatingRange}
          >
            {children}
            <SodaCan flavor={flavor} />
          </Float>
        )}
      </group>
    );
  },
);

FloatingCan.displayName = "FloatingCan";

export default FloatingCan;
