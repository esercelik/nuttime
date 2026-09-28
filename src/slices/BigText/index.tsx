"use client";
import Image from "next/image";
import { Content } from "@prismicio/client";
import { SliceComponentProps } from "@prismicio/react";
import { useLocale } from "@/i18n/LocaleProvider";
export default function BigText({ slice }: SliceComponentProps<Content.BigTextSlice>) {
  const { locale, t } = useLocale();
  return <section className="taste-editorial" data-slice-type={slice.slice_type}>
    <div className="editorial-photo"><Image src="/nuttime/pistachio-spoon.webp" alt={`${t.productNames[0]} — ${t.textureAlt}`} fill sizes="(max-width:768px) 100vw, 55vw" className="object-cover" /><span className="photo-caption">{t.shortNames[0]} · {new Intl.NumberFormat(locale, { style: "percent" }).format(.42)}</span></div>
    <div className="editorial-copy"><p className="eyebrow">{t.breakLabel}</p><h2>{t.editorialTitle[0]}<br /><em>{t.editorialTitle[1]}</em><br />{t.editorialTitle[2]}</h2><p>{t.editorialBody}</p><a href="#icerik" className="editorial-link">{t.discoverInside}<span aria-hidden="true">↗</span></a></div>
  </section>;
}
