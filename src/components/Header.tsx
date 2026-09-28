"use client";
import { useLocale } from "@/i18n/LocaleProvider";
import { isLocale, localeCodes, localePath, locales } from "@/i18n/config";
export default function Header() {
  const { locale, t } = useLocale();
  const home = localePath(locale);
  return <>
    <a className="skip-link" href="#main-content">{t.skip}</a>
    <header className="site-header">
      <a href={home} aria-label={t.home} className="brand-wordmark" dir="ltr">nuttime</a>
      <nav aria-label={t.menu}>
        <a href={`${home}#lezzetler`}>{t.navFlavors}</a>
        <a href={`${home}#rituel`} className="nav-ritual">{t.navRitual}</a>
        <a href={`${home}#icerik`}>{t.navInside}</a>
      </nav>
      <label className="language-control">
        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.4"><circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18M5 7h14M5 17h14"/></svg>
        <span className="sr-only">{t.language}</span>
        <select aria-label={t.language} value={locale} onChange={(event) => {
          const next = event.target.value;
          if (isLocale(next)) window.location.assign(localePath(next) + window.location.hash);
        }}>
          {localeCodes.map(code => <option key={code} value={code} lang={code}>{locales[code].label}</option>)}
        </select>
      </label>
    </header>
  </>;
}
