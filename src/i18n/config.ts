export const locales = {
  tr: { label: "Türkçe", direction: "ltr" },
  en: { label: "English", direction: "ltr" },
  de: { label: "Deutsch", direction: "ltr" },
  fr: { label: "Français", direction: "ltr" },
  es: { label: "Español", direction: "ltr" },
  it: { label: "Italiano", direction: "ltr" },
  ru: { label: "Русский", direction: "ltr" },
  ar: { label: "العربية", direction: "rtl" },
  zh: { label: "中文", direction: "ltr" },
  pt: { label: "Português", direction: "ltr" },
} as const;

export type Locale = keyof typeof locales;
export const localeCodes = Object.keys(locales) as Locale[];
export function isLocale(value: string | null | undefined): value is Locale {
  return !!value && Object.prototype.hasOwnProperty.call(locales, value);
}
export function localePath(locale: Locale): string {
  return locale === "tr" ? "/" : `/${locale}`;
}
