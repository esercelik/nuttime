import { getV2Content } from "@/lib/cms";
import type { Locale } from "@/i18n/config";
import { SliceZone } from "@prismicio/react";
import { Content, RichTextField } from "@prismicio/client";
import { components } from "@/slices";
import ProductDetails from "@/components/ProductDetails";
const rich = (text: string): RichTextField => [{ type: "paragraph", text, spans: [] }];
const base = { variation: "default", version: "initial", items: [], slice_label: null };
export default async function Storefront({ locale }: { locale: Locale }) {
const { dictionary: t } = await getV2Content(locale);
const slices = [
  { ...base, id: "nuttime-hero", slice_type: "hero", primary: {
    heading: rich(t.heroHeading), subheading: rich(t.heroSubheading),
    body: rich(t.heroBody),
    button_text: t.chooseFlavor, button_link: { link_type: "Web", url: "#lezzetler" },
    cans_image: { url: "/nuttime/coconut.png", dimensions: { width: 1707, height: 2560 }, alt: t.productNames[2], copyright: null },
    second_heading: rich(t.secondHeading),
    second_body: rich(t.secondBody),
  }} as Content.HeroSlice,
  { ...base, id: "nuttime-carousel", slice_type: "carousel", primary: {
    heading: rich(t.carouselHeading), price_copy: rich(t.weightTexture),
  }} as Content.CarouselSlice,
  { ...base, id: "nuttime-story", slice_type: "alternating_text", primary: { text_group: [
    { heading: rich(t.ritualHeadings[0]), body: rich(t.ritualBodies[0]) },
    { heading: rich(t.ritualHeadings[1]), body: rich(t.ritualBodies[1]) },
    { heading: rich(t.ritualHeadings[2]), body: rich(t.ritualBodies[2]) },
  ] }} as Content.AlternatingTextSlice,
  { ...base, id: "nuttime-skydive", slice_type: "sky_dive", primary: { sentence: t.dive, flavor: "lemonLime" }} as Content.SkyDiveSlice,
  { ...base, id: "nuttime-bigtext", slice_type: "big_text", primary: {} } as Content.BigTextSlice,
];
return <><SliceZone slices={slices} components={components} /><ProductDetails /></>; }
