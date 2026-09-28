"use client";

import { Canvas, useThree } from "@react-three/fiber";
import { PerformanceMonitor, View } from "@react-three/drei";
import { Suspense, useEffect, useState } from "react";
import { NeutralToneMapping, SRGBColorSpace } from "three";
import dynamic from "next/dynamic";
import { useMediaQuery } from "@/hooks/useMediaQuery";

const Loader = dynamic(
  () => import("@react-three/drei").then((mod) => mod.Loader),
  { ssr: false },
);

function VisibilityBudget() {
  const setFrameloop = useThree((state) => state.setFrameloop);
  useEffect(() => {
    const update = () => setFrameloop(document.hidden ? "never" : "always");
    document.addEventListener("visibilitychange", update);
    update();
    return () => document.removeEventListener("visibilitychange", update);
  }, [setFrameloop]);
  return null;
}

export default function ViewCanvas() {
  const mobile = useMediaQuery("(max-width: 767px)", false);
  const [quality, setQuality] = useState(1.75);
  return (
    <>
      <Canvas
        style={{
          position: "fixed",
          top: 0,
          left: "50%",
          transform: "translateX(-50%)",
          overflow: "hidden",
          pointerEvents: "none",
          zIndex: 30,
        }}
        shadows
        dpr={[1, Math.min(quality, mobile ? 1.5 : 1.75)]}
        gl={{
          antialias: true,
          alpha: true,
          toneMapping: NeutralToneMapping,
          outputColorSpace: SRGBColorSpace,
          toneMappingExposure: 1,
        }}
        camera={{ fov: 30 }}
      >
        <VisibilityBudget />
        <PerformanceMonitor
          onDecline={() => setQuality(1.25)}
          onIncline={() => setQuality(1.75)}
          flipflops={3}
          onFallback={() => setQuality(1.25)}
        />
        <Suspense fallback={null}>
          <View.Port />
        </Suspense>
      </Canvas>
      <Loader />
    </>
  );
}
