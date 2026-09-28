"use client";
import { useThree } from "@react-three/fiber";
import { useTexture } from "@react-three/drei";
import { useEffect, useLayoutEffect, useMemo, useRef } from "react";
import * as THREE from "three";

const products = {
  blackCherry: { image: "/nuttime/pistachio.jpg", cream: "#a5a33e", cx: 720, rx: 296, top: 1075, topCurve: 62, bottom: 1428, bottomCurve: 69 },
  lemonLime: { image: "/nuttime/coconut.png", cream: "#ece4d2", cx: 687, rx: 317, top: 1250, topCurve: 51, bottom: 1650, bottomCurve: 88 },
  grape: { image: "/nuttime/hazelnut.jpg", cream: "#b58a58", cx: 680, rx: 306, top: 990, topCurve: 80, bottom: 1410, bottomCurve: 69 },
  strawberryLemonade: { image: "/nuttime/almond.jpg", cream: "#cda16e", cx: 703, rx: 315, top: 1010, topCurve: 78, bottom: 1450, bottomCurve: 67 },
  watermelon: { image: "/nuttime/peanut.jpg", cream: "#c6995e", cx: 700, rx: 316, top: 1135, topCurve: 40, bottom: 1580, bottomCurve: 44 },
};
export type SodaCanProps = { flavor?: keyof typeof products; scale?: number; };


function detailTexture(kind: "grain" | "wood"): THREE.DataTexture {
  const width = 256, height = 128;
  const data = new Uint8Array(width * height * 4);
  for (let y = 0; y < height; y++) {
    for (let x = 0; x < width; x++) {
      const random = Math.sin(x * 12.9898 + y * 78.233) * 43758.5453;
      const noise = random - Math.floor(random);
      const wave = Math.sin(y * .74 + Math.sin(x * .022) * 3 + Math.sin(x * .047) * .6);
      const value = kind === "wood" ? 226 + wave * 17 + noise * 7 : 216 + noise * 36;
      const offset = (y * width + x) * 4;
      data.set([value, value, value, 255], offset);
    }
  }
  const texture = new THREE.DataTexture(data, width, height);
  texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
  texture.magFilter = THREE.LinearFilter;
  texture.minFilter = THREE.LinearMipmapLinearFilter;
  texture.generateMipmaps = true;
  texture.needsUpdate = true;
  return texture;
}

function mapWoodGrain(geometry: THREE.BufferGeometry): void {
  const position = geometry.attributes.position;
  const uv: number[] = [];
  for (let i = 0; i < position.count; i++) {
    uv.push((position.getX(i) + .25) / 1.3, position.getZ(i) / .36 + .5);
  }
  geometry.setAttribute("uv", new THREE.Float32BufferAttribute(uv, 2));
}

class GlassThreadCurve extends THREE.Curve<THREE.Vector3> {
  constructor(private radius = .03365, private base = .069, private rise = .0047, private turns = 2.1) { super(); }
  getPoint(t: number, target = new THREE.Vector3()): THREE.Vector3 {
    const angle = t * Math.PI * 2 * this.turns;
    return target.set(Math.cos(angle) * this.radius, this.base + t * this.rise, Math.sin(angle) * this.radius);
  }
}

/** Packed linear data: red is micro-height; green is material roughness. */
function finishTexture(kind: "glass" | "lid" | "label"): THREE.DataTexture {
  const size = 128;
  const data = new Uint8Array(size * size * 4);
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      const phase = Math.sin(x * 12.9898 + y * 78.233) * 43758.5453;
      const grain = phase - Math.floor(phase);
      const broad = Math.sin(x / size * Math.PI * 8) * Math.cos(y / size * Math.PI * 6);
      const height = kind === "glass" ? 128 + broad * 17 + grain * 3 : 120 + grain * 16;
      const roughness = kind === "glass" ? 205 + broad * 15 + grain * 10 : 220 + grain * 25;
      data.set([height, roughness, 255, 255], (y * size + x) * 4);
    }
  }
  const texture = new THREE.DataTexture(data, size, size);
  texture.colorSpace = THREE.NoColorSpace;
  texture.wrapS = texture.wrapT = THREE.RepeatWrapping;
  texture.minFilter = THREE.LinearMipmapLinearFilter;
  texture.magFilter = THREE.LinearFilter;
  texture.generateMipmaps = true;
  texture.repeat.set(kind === "glass" ? 2 : 6, kind === "label" ? 2 : 1);
  texture.needsUpdate = true;
  return texture;
}

/** Preserve photographed ink; color alone does not establish metallic foil. */
function printedLabel(map: THREE.Texture, micrograin: THREE.Texture): THREE.MeshPhysicalMaterial {
  const material = new THREE.MeshPhysicalMaterial({
    map, roughness: .67, roughnessMap: micrograin, bumpMap: micrograin,
    bumpScale: .000003, metalness: 0, clearcoat: 0, ior: 1.46,
    specularIntensity: .35,
  });
  material.onBeforeCompile = (shader) => {
    shader.fragmentShader = shader.fragmentShader.replace("#include <roughnessmap_fragment>", `
      #include <roughnessmap_fragment>
      float printLuminance = dot(diffuseColor.rgb, vec3(.2126, .7152, .0722));
      roughnessFactor = clamp(roughnessFactor + .025 * printLuminance, .5, .8);
    `);
  };
  material.customProgramCacheKey = () => "nuttime-printed-label-v2";
  return material;
}

// Retains the upstream scene API; all geometry below is a 74 × 85 mm Nuttime jar.
export function SodaCan({ flavor = "blackCherry", scale = 2 }: SodaCanProps) {
  const product = products[flavor];
  const renderer = useThree((state) => state.gl);
  const anisotropy = Math.min(8, renderer.capabilities.getMaxAnisotropy());
  const [texture, backTexture] = useTexture([product.image, "/nuttime/coconut-back.jpg"]);
  const jar = useMemo(() => {
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.anisotropy = anisotropy;
    texture.minFilter = THREE.LinearMipmapLinearFilter;
    const micrograin = finishTexture("label");
    const glassFinish = finishTexture("glass");
    const lidFinish = finishTexture("lid");
    const group = new THREE.Group();
    const glass = new THREE.MeshPhysicalMaterial({
      color: "#ffffff", roughness: .085, roughnessMap: glassFinish, bumpMap: glassFinish, bumpScale: .000007, metalness: 0, ior: 1.52,
      transmission: 1, thickness: .0024, attenuationColor: "#f1f6ee", attenuationDistance: .8 * scale * 6.2,
      transparent: true, opacity: 1, depthWrite: false,
      clearcoat: 0, envMapIntensity: .9,
    });
    // Three r167 clears the refraction buffer to white at half alpha. Remove that
    // matte before compositing over the page's CSS background, preserving reflections.
    glass.onBeforeCompile = (shader) => {
      shader.vertexShader = "varying float vJarHeight;\n" + shader.vertexShader;
      shader.vertexShader = shader.vertexShader.replace("#include <begin_vertex>", "#include <begin_vertex>\nvJarHeight = position.y;");
      shader.fragmentShader = "varying float vJarHeight;\n" + shader.fragmentShader;
      const transmission = THREE.ShaderChunk.transmission_fragment.replace(
        "material.thickness = thickness;",
        "material.thickness = thickness * mix(1.85, 1.0, smoothstep(.005, .013, vJarHeight));",
      );
      shader.fragmentShader = shader.fragmentShader.replace("#include <transmission_fragment>", transmission);
      const refraction = THREE.ShaderChunk.transmission_pars_fragment
        .replace("#ifdef USE_TRANSMISSION", "#ifdef USE_TRANSMISSION\nfloat jarCoverage = 1.0;")
        .replace("vec3 attenuatedColor = transmittance * transmittedLight.rgb;", `
          jarCoverage = clamp((transmittedLight.a - .5) * 2.0, 0.0, 1.0);
          transmittedLight.rgb = max(transmittedLight.rgb - vec3(1.0 - jarCoverage), vec3(0.0)) / max(jarCoverage, .001);
          transmittedLight.a = jarCoverage;
          vec3 attenuatedColor = transmittance * transmittedLight.rgb;
        `);
      shader.fragmentShader = shader.fragmentShader.replace("#include <transmission_pars_fragment>", refraction)
        .replace("#include <opaque_fragment>", `
          float facing = clamp(abs(dot(normal, normalize(vViewPosition))), 0.0, 1.0);
          float fresnel = .0426 + .9574 * pow(1.0 - facing, 5.0);
          float glassAlpha = clamp(max(material.transmissionAlpha, fresnel), .025, 1.0);
          outgoingLight = (totalDiffuse * jarCoverage + totalSpecular) / glassAlpha;
          material.transmissionAlpha = glassAlpha;
          #include <opaque_fragment>
        `);
    };
    glass.customProgramCacheKey = () => "nuttime-refraction-r167-v2";
    const glassEdge = glass.clone();
    glassEdge.onBeforeCompile = glass.onBeforeCompile;
    glassEdge.customProgramCacheKey = glass.customProgramCacheKey;
    glassEdge.roughness = .095;
    glassEdge.thickness = .00076;
    const random = (seed: number): number => {
      const value = Math.sin(seed * 127.1 + 311.7) * 43758.5453;
      return value - Math.floor(value);
    };
    const noise = (x: number, y: number, cells: number): number => {
      const ix = Math.floor(x), iy = Math.floor(y);
      const sx = (x - ix) ** 2 * (3 - 2 * (x - ix));
      const sy = (y - iy) ** 2 * (3 - 2 * (y - iy));
      const sample = (a: number, b: number) => random((a % cells) + (b % cells) * 131);
      return THREE.MathUtils.lerp(
        THREE.MathUtils.lerp(sample(ix, iy), sample(ix + 1, iy), sx),
        THREE.MathUtils.lerp(sample(ix, iy + 1), sample(ix + 1, iy + 1), sx), sy,
      );
    };
    const surfaceData = new Uint8Array(256 * 256 * 4);
    const foodColorData = new Uint8Array(256 * 256 * 4);
    for (let y = 0; y < 256; y++) {
      for (let x = 0; x < 256; x++) {
        const coarse = noise(x / 32, y / 32, 8);
        const medium = noise(x / 8, y / 8, 32);
        const fine = noise(x / 2, y / 2, 128);
        const height = Math.round(100 + coarse * 40 + medium * 30 + fine * 24);
        const roughness = Math.round(210 + coarse * 22 - fine * 18);
        const color = Math.round(240 + coarse * 7 + medium * 5 + fine * 3);
        surfaceData.set([height, roughness, 255, 255], (y * 256 + x) * 4);
        foodColorData.set([color, color - Math.round(medium * 2), color - Math.round(coarse * 4), 255], (y * 256 + x) * 4);
      }
    }
    const surface = new THREE.DataTexture(surfaceData, 256, 256);
    surface.wrapS = surface.wrapT = THREE.RepeatWrapping;
    surface.magFilter = THREE.LinearFilter;
    surface.minFilter = THREE.LinearMipmapLinearFilter;
    surface.generateMipmaps = true;
    surface.needsUpdate = true;
    surface.colorSpace = THREE.NoColorSpace;
    const foodColor = surface.clone();
    foodColor.image = { data: foodColorData, width: 256, height: 256 };
    foodColor.colorSpace = THREE.SRGBColorSpace;
    foodColor.needsUpdate = true;
    const cream = new THREE.MeshPhysicalMaterial({ color: product.cream, map: foodColor, roughness: .48, roughnessMap: surface, bumpMap: surface, bumpScale: .000065, ior: 1.43, clearcoat: .07, clearcoatRoughness: .42 });
    const black = new THREE.MeshPhysicalMaterial({ color: "#141516", roughness: .38, roughnessMap: lidFinish, bumpMap: lidFinish, bumpScale: .000004, metalness: 0, ior: 1.48, clearcoat: .04, clearcoatRoughness: .4 });
    const sleeve = new THREE.MeshStandardMaterial({ color: "#181719", roughness: .8 });
    const mesh = (name: string, geometry: THREE.BufferGeometry, material: THREE.Material, parent: THREE.Group = group) => {
      const object = new THREE.Mesh(geometry, material); object.name = name;
      object.castShadow = material !== glass && material !== glassEdge; object.receiveShadow = object.castShadow; parent.add(object); return object;
    };
    const fillHeight = .0715;
    const fillRadius = .03085;
    const surfaceHeight = (x: number, z: number): number => {
      const radius = Math.hypot(x, z), angle = Math.atan2(z, x);
      const swirl = radius * Math.sin(radius * 19 - angle * 2);
      const mound = Math.exp(-((x + .23) ** 2 + (z - .1) ** 2) * 5) * .0011;
      return Math.max(0, 1 - radius * radius) * (.0006 + .00035 * swirl + mound);
    };
    const fillProfile = new THREE.Path();
    fillProfile.moveTo(0, .00505);
    fillProfile.lineTo(.029, .00505);
    fillProfile.quadraticCurveTo(.03415, .00505, .03415, .010);
    fillProfile.lineTo(.03415, .055);
    fillProfile.bezierCurveTo(.03415, .061, fillRadius, .063, fillRadius, .068);
    fillProfile.lineTo(fillRadius, fillHeight);
    mesh("Spread", new THREE.LatheGeometry(fillProfile.getPoints(12), 96), cream);
    const spreadTop = new THREE.BufferGeometry();
    const vertices: number[] = [], topUvs: number[] = [], indices: number[] = [];
    const rings = 24, segments = 80;
    for (let ring = 0; ring <= rings; ring++) {
      const radius = ring / rings;
      for (let segment = 0; segment <= segments; segment++) {
        const angle = segment / segments * Math.PI * 2;
        const x = Math.cos(angle) * radius, z = Math.sin(angle) * radius;
        const height = surfaceHeight(x, z);
        vertices.push(x * fillRadius, height, z * fillRadius);
        topUvs.push(x * .5 + .5, z * .5 + .5);
        if (ring < rings && segment < segments) {
          const a = ring * (segments + 1) + segment, b = a + segments + 1;
          indices.push(a, a + 1, b, a + 1, b + 1, b);
        }
      }
    }
    spreadTop.setAttribute("position", new THREE.Float32BufferAttribute(vertices, 3));
    spreadTop.setAttribute("uv", new THREE.Float32BufferAttribute(topUvs, 2));
    spreadTop.setIndex(indices);
    spreadTop.computeVertexNormals();
    mesh("SpreadSurface", spreadTop, cream).position.y = fillHeight;
    const morsels = new THREE.InstancedMesh(new THREE.IcosahedronGeometry(1, 1), new THREE.MeshStandardMaterial({ roughness: .47 }), 112);
    morsels.name = "SpreadMorsels";
    const morsel = new THREE.Object3D();
    const morselColor = new THREE.Color();
    for (let i = 0; i < 112; i++) {
      const angle = random(i * 7 + 2) * Math.PI * 2;
      const radius = Math.sqrt(random(i * 7 + 3)) * .96;
      const x = Math.cos(angle) * radius, z = Math.sin(angle) * radius;
      const size = .00022 + random(i * 7 + 4) ** 2 * .00062;
      morsel.position.set(x * fillRadius, fillHeight + surfaceHeight(x, z) - size * .34, z * fillRadius);
      morsel.scale.set(size, size * .55, size * (.5 + random(i * 7 + 5) * .7));
      morsel.rotation.set(random(i * 7 + 6), angle, random(i * 7 + 7));
      morsel.updateMatrix();
      morsels.setMatrixAt(i, morsel.matrix);
      morselColor.set(product.cream).offsetHSL(0, -.012, (random(i * 7 + 8) - .55) * .085);
      morsels.setColorAt(i, morselColor);
    }
    morsels.instanceMatrix.needsUpdate = true;
    if (morsels.instanceColor) morsels.instanceColor.needsUpdate = true;
    morsels.computeBoundingSphere();
    group.add(morsels);
    const glassProfile = new THREE.Path();
    glassProfile.moveTo(0, .001);
    glassProfile.lineTo(.027, .001);
    glassProfile.bezierCurveTo(.034, .001, .0367, .0035, .0367, .011);
    glassProfile.lineTo(.0367, .055);
    glassProfile.bezierCurveTo(.0367, .062, .0335, .063, .0335, .068);
    glassProfile.lineTo(.0335, .074);
    glassProfile.quadraticCurveTo(.0335, .0756, .0322, .0756);
    glassProfile.quadraticCurveTo(.0309, .0756, .0309, .074);
    glassProfile.lineTo(.0309, .068);
    glassProfile.bezierCurveTo(.0309, .063, .0342, .061, .0342, .055);
    glassProfile.lineTo(.0342, .010);
    glassProfile.quadraticCurveTo(.0342, .005, .029, .005);
    glassProfile.lineTo(0, .005);
    mesh("Glass", new THREE.LatheGeometry(glassProfile.getPoints(8), 128), glass);
    // Follow the shoulder so the photographed paper never floats above the glass.
    const shoulder = new THREE.CubicBezierCurve(new THREE.Vector2(.0367, .055), new THREE.Vector2(.0367, .062), new THREE.Vector2(.0335, .063), new THREE.Vector2(.0335, .068)).getPoints(120);
    const conformLabel = (geometry: THREE.CylinderGeometry, thickness: number) => {
      const positions = geometry.attributes.position;
      for (let i = 0; i < positions.count; i++) {
        const height = positions.getY(i) + .035;
        let radius = .0367;
        if (height > .055) {
          const next = shoulder.findIndex(point => point.y >= height);
          if (next > 0) {
            const a = shoulder[next - 1], b = shoulder[next];
            radius = THREE.MathUtils.lerp(a.x, b.x, (height - a.y) / (b.y - a.y));
          }
        }
        const factor = (radius + thickness) / Math.hypot(positions.getX(i), positions.getZ(i));
        positions.setXYZ(i, positions.getX(i) * factor, positions.getY(i), positions.getZ(i) * factor);
      }
      geometry.computeVertexNormals();
      return geometry;
    };
    const labelHeight = .05;
    if (flavor !== "lemonLime") {
      mesh("LabelSleeve", conformLabel(new THREE.CylinderGeometry(.0367,.0367,labelHeight,96,12,true), .00004), sleeve).position.y = .035;
    }
    const width = 2.5;
    const label = new THREE.CylinderGeometry(.03685,.03685,labelHeight,96,12,true,-width/2,width);
    const uv = label.attributes.uv;
    for (let i = 0; i < uv.count; i++) {
      const angle = (uv.getX(i)-.5)*width;
      const x = product.cx + Math.sin(angle)*product.rx;
      const upper = product.top + Math.cos(angle)*product.topCurve;
      const lower = product.bottom + Math.cos(angle)*product.bottomCurve;
      uv.setXY(i, x/1279, 1-(lower+(upper-lower)*uv.getY(i))/1919);
    }
    mesh("OriginalPhotoLabel", conformLabel(label, .00008), printedLabel(texture, micrograin)).position.y = .035;
    if (flavor === "lemonLime") {
      backTexture.colorSpace = THREE.SRGBColorSpace;
      backTexture.anisotropy = anisotropy;
      const backLabel = new THREE.CylinderGeometry(.03685,.03685,labelHeight,96,12,true,-width/2,width);
      const backUv = backLabel.attributes.uv;
      for (let i = 0; i < backUv.count; i++) {
        const angle = (backUv.getX(i)-.5)*width;
        const upper = 876 + Math.cos(angle)*25;
        const lower = 1154 + Math.cos(angle)*55;
        backUv.setXY(i,(567+Math.sin(angle)*216)/1200,1-(lower+(upper-lower)*backUv.getY(i))/1600);
      }
      const rear = mesh("OriginalRearLabel", conformLabel(backLabel, .00008), printedLabel(backTexture, micrograin));
      rear.position.y=.035; rear.rotation.y=Math.PI;
    }
    const collar = mesh("GlassShoulderBead", new THREE.TorusGeometry(.0354, .00028, 8, 96), glassEdge);
    collar.rotation.x = Math.PI / 2;
    collar.position.y = .061;
    mesh("GlassThread", new THREE.TubeGeometry(new GlassThreadCurve(), 128, .00038, 6, false), glassEdge);
    const lid = new THREE.Group(); lid.name = "NuttimeLid"; lid.position.y=.073; group.add(lid);
    const capProfile = new THREE.Path();
    capProfile.moveTo(.0344, 0);
    capProfile.lineTo(.0359, 0);
    capProfile.quadraticCurveTo(.0371, 0, .0371, .0016);
    capProfile.lineTo(.0371, .0088);
    capProfile.bezierCurveTo(.0371, .0108, .0363, .0115, .0348, .0115);
    capProfile.lineTo(0, .0115);
    capProfile.lineTo(0, .0095);
    capProfile.lineTo(.0336, .0095);
    capProfile.quadraticCurveTo(.0344, .0095, .0344, .0087);
    capProfile.lineTo(.0344, 0);
    mesh("BlackLid", new THREE.LatheGeometry(capProfile.getPoints(8), 128), black, lid);
    const topFinish = new THREE.MeshPhysicalMaterial({ color: "#141516", metalness: 0, roughness: .4, roughnessMap: lidFinish, bumpMap: lidFinish, bumpScale: .000003, clearcoat: .03 });
    const topPanelGeometry = new THREE.CircleGeometry(.0235, 96);
    if (flavor === "lemonLime") {
      topFinish.map = texture;
      topFinish.color.set("#ffffff");
      const position = topPanelGeometry.attributes.position;
      const uv = topPanelGeometry.attributes.uv;
      for (let i = 0; i < uv.count; i++) {
        uv.setXY(i, (674 + position.getX(i) / .0235 * 189) / 1279, 1 - (972 - position.getY(i) / .0235 * 37) / 1919);
      }
    }
    const topPanel = mesh("LidInsetTop", topPanelGeometry, topFinish, lid);
    topPanel.rotation.x = -Math.PI / 2;
    topPanel.position.y = .01153;
    const innerFinish = new THREE.MeshStandardMaterial({ color: "#171818", roughness: .52, metalness: .08 });
    mesh("LidInnerThread",new THREE.TubeGeometry(new GlassThreadCurve(.0341,.0018,.0048,1.55),128,.00032,8,false),innerFinish,lid);
    const sealingRidge = mesh("LidSealSeat",new THREE.TorusGeometry(.0332,.0003,10,96),innerFinish,lid);
    sealingRidge.rotation.x = Math.PI / 2; sealingRidge.position.y = .0082;
    const seal = mesh("LidInnerSeal", new THREE.RingGeometry(.030,.0336,80),new THREE.MeshStandardMaterial({color:"#d7d0bd",roughness:.8,side:THREE.DoubleSide}),lid);
    seal.rotation.x = Math.PI / 2; seal.position.y = .0088;
    for (const y of [.001,.01]) { const rim=mesh("LidRim",new THREE.TorusGeometry(.0364,.0005,12,128),black,lid); rim.rotation.x=Math.PI/2;rim.position.y=y; }
    group.userData.ownedTextures = [surface, foodColor, micrograin, glassFinish, lidFinish];
    return group;
  }, [texture, backTexture, product, flavor, anisotropy, scale]);
  useEffect(() => () => {
    const materials = new Set<THREE.Material>();
    jar.traverse((object) => {
      if (object instanceof THREE.InstancedMesh) object.dispose();
      if (object instanceof THREE.Mesh) {
        object.geometry.dispose();
        const list = Array.isArray(object.material) ? object.material : [object.material];
        list.forEach((material) => materials.add(material));
      }
    });
    materials.forEach((material) => material.dispose());
    jar.userData.ownedTextures.forEach((map: THREE.Texture) => map.dispose());
  }, [jar]);
  return <group scale={scale * 6.2}><primitive object={jar} position-y={-.0425} /></group>;
}

export function NuttimeSpoon() {
  const wood = useMemo(() => detailTexture("wood"), []);
  const spoonGrain = useMemo(() => detailTexture("grain"), []);
  const flakes = useRef<THREE.InstancedMesh>(null);
  useEffect(() => () => { wood.dispose(); spoonGrain.dispose(); }, [wood, spoonGrain]);
  useLayoutEffect(() => {
    if (!flakes.current) return;
    const object = new THREE.Object3D();
    const color = new THREE.Color();
    for (let i = 0; i < 36; i++) {
      const angle = i * 2.399963, radius = Math.sqrt((i + .5) / 36) * .87;
      const x = Math.cos(angle) * radius, z = Math.sin(angle) * radius;
      object.position.set(x * .205, Math.sqrt(1 - radius * radius) * .077, z * .127);
      object.rotation.set(i * .7, i * .9, i * .4);
      object.scale.set(.009 + (i % 3) * .002, .0035, .006);
      object.updateMatrix();
      flakes.current.setMatrixAt(i, object.matrix);
      flakes.current.setColorAt(i, color.set(i % 3 === 0 ? "#eaddbf" : "#fff5e3"));
    }
    flakes.current.instanceMatrix.needsUpdate = true;
    if (flakes.current.instanceColor) flakes.current.instanceColor.needsUpdate = true;
    flakes.current.computeBoundingSphere();
  }, []);
  const bowl = useMemo(() => {
    const profile = [
      [0, -.043], [.08, -.040], [.15, -.024], [.195, .007],
      [.205, .024], [.201, .032], [.193, .034], [.184, .023],
      [.145, -.003], [.08, -.021], [0, -.025],
    ];
    const geometry = new THREE.LatheGeometry(
      new THREE.SplineCurve(profile.map(([x, y]) => new THREE.Vector2(x, y))).getPoints(80), 80,
    );
    geometry.scale(1.18, 1, .78);
    mapWoodGrain(geometry);
    const position = geometry.attributes.position;
    const colors = [];
    const color = new THREE.Color();
    for (let i = 0; i < position.count; i++) {
      const x = position.getX(i), z = position.getZ(i);
      const grain = Math.sin(z * 380 + Math.sin(x * 17) * 2) * .025;
      color.setHSL(.085, .40, .43 + grain);
      colors.push(color.r, color.g, color.b);
    }
    geometry.setAttribute("color", new THREE.Float32BufferAttribute(colors, 3));
    geometry.computeVertexNormals();
    return geometry;
  }, []);
  const handle = useMemo(() => {
    const vertices: number[] = [], indices: number[] = [];
    for (let i = 0; i <= 64; i++) {
      const t = i / 64;
      const width = (.024 + .024 * Math.sin(Math.PI * t * .85)) * Math.min(1, (1 - t) * 25 + .02);
      for (let j = 0; j <= 20; j++) {
        const angle = j / 20 * Math.PI * 2;
        vertices.push(.18 + t * .83, .015 + Math.sin(t * Math.PI * .65) * .085 + Math.cos(angle) * .014 * Math.min(1, (1 - t) * 30 + .02), Math.sin(angle) * width);
        if (i < 64 && j < 20) {
          const a = i * 21 + j, b = a + 21;
          indices.push(a, a + 1, b, b, a + 1, b + 1);
        }
      }
    }
    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute("position", new THREE.Float32BufferAttribute(vertices, 3));
    geometry.setIndex(indices);
    mapWoodGrain(geometry);
    const color = new THREE.Color();
    const colors: number[] = [];
    for (let i = 0; i < vertices.length; i += 3) {
      const grain = Math.sin(vertices[i + 2] * 520 + Math.sin(vertices[i] * 9) * 2) * .018;
      color.setHSL(.09, .38, .48 + grain);
      colors.push(color.r, color.g, color.b);
    }
    geometry.setAttribute("color", new THREE.Float32BufferAttribute(colors, 3));
    geometry.computeVertexNormals();
    return geometry;
  }, []);
  const cream = useMemo(() => {
    const geometry = new THREE.SphereGeometry(1, 56, 32);
    const position = geometry.attributes.position;
    for (let i = 0; i < position.count; i++) {
      const x = position.getX(i), y = position.getY(i), z = position.getZ(i);
      const ripple = 1 + .035 * Math.sin(x * 19 + z * 9) * Math.sin(y * 14 + x * 7);
      position.setXYZ(i, x * ripple, y * ripple, z * ripple);
    }
    geometry.computeVertexNormals();
    return geometry;
  }, []);
  return <group name="NuttimeSpoon">
    <mesh geometry={bowl} castShadow receiveShadow>
      <meshPhysicalMaterial vertexColors map={wood} bumpMap={wood} bumpScale={.0008} roughness={.5} clearcoat={.16} clearcoatRoughness={.45} side={THREE.DoubleSide} />
    </mesh>
    <mesh geometry={handle} castShadow receiveShadow>
      <meshPhysicalMaterial vertexColors roughness={.48} clearcoat={.16} side={THREE.DoubleSide} />
    </mesh>
    <group name="Spoonful" position={[0, .014, 0]}>
      <mesh geometry={cream} scale={[.218, .078, .137]} castShadow receiveShadow>
        <meshPhysicalMaterial color="#efe5ce" roughness={.47} bumpMap={spoonGrain} bumpScale={.00035} clearcoat={.07} clearcoatRoughness={.42} />
      </mesh>
      <instancedMesh ref={flakes} args={[undefined, undefined, 36]}>
        <icosahedronGeometry args={[1, 1]} />
        <meshStandardMaterial roughness={.58} />
      </instancedMesh>
    </group>
  </group>;
}
