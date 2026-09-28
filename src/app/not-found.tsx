"use client";
import { useLocale } from "@/i18n/LocaleProvider";
import { localePath } from "@/i18n/config";
export default function NotFound() {
  const { locale, t } = useLocale();
  return <section className="not-found"><span className="eyebrow">{t.notFoundLabel}</span><h1>{t.notFoundHeading}</h1><p>{t.notFoundBody}</p><a href={`${localePath(locale)}#lezzetler`}>{t.exploreFlavors}</a></section>;
}
