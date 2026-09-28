"use client";
import { useLocale } from "@/i18n/LocaleProvider";
import { localeCodes, localePath, locales } from "@/i18n/config";
export default function Footer() {
  const { locale, t } = useLocale();
  return <footer className="site-footer">
    <div className="footer-top"><div><p className="eyebrow">NUTTIME</p><h2>{t.footerTagline}</h2></div><a className="footer-cta" href={`${localePath(locale)}#lezzetler`}>{t.exploreFlavors}</a></div>
    <div className="footer-language"><span>{t.language}</span><nav aria-label={t.language}>{localeCodes.map(code => <a key={code} href={localePath(code)} hrefLang={code} lang={code} aria-current={code === locale ? "page" : undefined}>{locales[code].label}</a>)}</nav></div>
    <div className="footer-bottom"><a href={localePath(locale)} className="brand-wordmark" dir="ltr" aria-label={t.home}>nuttime</a><span>© {new Date().getFullYear()} Nuttime</span><a href={`${localePath(locale)}#`}>{t.backTop}</a></div>
  </footer>;
}
