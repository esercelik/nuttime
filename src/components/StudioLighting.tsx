"use client";

import { Environment, Lightformer, PerspectiveCamera } from "@react-three/drei";
import { useFrame } from "@react-three/fiber";
import { useMemo, useRef } from "react";
import { PerspectiveCamera as ThreePerspectiveCamera } from "three";

/** Fixed neutral studio: broad key, narrow rim, low fill and unlit negative fill. */
export default function StudioLighting() {
  return (
    <>
      <Environment resolution={256} frames={1} environmentIntensity={0.85}>
        <color attach="background" args={["#121212"]} />
        <Lightformer
          form="rect"
          intensity={4}
          color="#fffdf8"
          position={[-3, 2.8, 4]}
          scale={[2.8, 4.5, 1]}
          target={[0, 0, 0]}
        />
        <Lightformer
          form="rect"
          intensity={0.65}
          color="#ffffff"
          position={[3, 1, 4]}
          scale={[2, 3, 1]}
          target={[0, 0, 0]}
        />
        <Lightformer
          form="rect"
          intensity={3.2}
          color="#ffffff"
          position={[3, 2, -2]}
          scale={[0.65, 4, 1]}
          target={[0, 0, 0]}
        />
        <Lightformer
          form="rect"
          intensity={1.5}
          color="#ffffff"
          position={[0, 5, 0]}
          scale={[3, 2, 1]}
          target={[0, 0, 0]}
        />
      </Environment>
      <directionalLight
        position={[-3, 5, 5]}
        intensity={2.1}
        color="#fffdf8"
        castShadow
        shadow-mapSize={[1024, 1024]}
        shadow-camera-left={-3}
        shadow-camera-right={3}
        shadow-camera-top={3}
        shadow-camera-bottom={-3}
        shadow-camera-near={0.1}
        shadow-camera-far={20}
        shadow-bias={-0.0001}
        shadow-normalBias={0.0008}
        shadow-radius={2}
      />
      <directionalLight position={[4, 1, 3]} intensity={0.18} color="#ffffff" />
    </>
  );
}

/** 36 mm sensor / 85 mm lens; fit both axes without changing the product scale. */
export function ProductCamera({
  frameHeight = 2,
  frameWidth = 1.6,
  target = [0, 0, 0],
  elevation = 0.12,
}: {
  frameHeight?: number;
  frameWidth?: number;
  target?: [number, number, number];
  elevation?: number;
}) {
  const camera = useRef<ThreePerspectiveCamera>(null);
  useFrame(() => {
    if (!camera.current) return;
    const current = camera.current;
    current.filmGauge = 36;
    current.setFocalLength(85);
    const height = Math.max(frameHeight, frameWidth / current.aspect);
    const distance = height / (2 * Math.tan((current.fov * Math.PI) / 360));
    current.position.set(
      target[0],
      target[1] + distance * elevation,
      target[2] + distance,
    );
    current.lookAt(...target);
    current.updateMatrixWorld();
  });
  return (
    <PerspectiveCamera
      ref={camera}
      makeDefault
      near={0.02}
      far={80}
      position={[0, 0.6, 6]}
    />
  );
}

/** Tight contact and directional penumbra, in the same plane as the glass base. */
export function ProductShadow({
  opacity = 0.3,
  scale = 1,
  position = [0, -0.5146, 0],
}: {
  opacity?: number;
  scale?: number;
  position?: [number, number, number];
}) {
  const uniforms = useMemo(() => ({ opacity: { value: opacity } }), [opacity]);
  return (
    <mesh
      position={position}
      rotation={[-Math.PI / 2, 0, 0]}
      scale={[scale * 2.6, scale * 2.2, 1]}
    >
      <planeGeometry args={[1, 1]} />
      <shaderMaterial
        transparent
        depthWrite={false}
        toneMapped={false}
        uniforms={uniforms}
        vertexShader={`varying vec2 vUv; void main() { vUv = uv; gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0); }`}
        fragmentShader={`uniform float opacity; varying vec2 vUv;
          void main() {
            vec2 p = (vUv - .5) * vec2(2.6, 2.2);
            float contact = exp(-pow(length(p) / .46, 6.0));
            float penumbra = exp(-dot((p - vec2(.16,-.13)) / vec2(.65,.52), (p - vec2(.16,-.13)) / vec2(.65,.52)) * 2.5);
            float edge = 1.0 - smoothstep(.4, .5, max(abs(vUv.x-.5), abs(vUv.y-.5)));
            gl_FragColor = vec4(.055,.047,.032, min(.65, opacity * (contact + .52 * penumbra)) * edge);
          }`}
      />
    </mesh>
  );
}
