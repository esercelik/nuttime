"use client";
import { createContext, useContext } from "react";
import type { Locale } from "./config";
import type { Dictionary } from "./dictionaries";
import type { CmsProduct } from "@/lib/cms";
const LocaleContext = createContext<{ locale: Locale; t: Dictionary; products: CmsProduct[] } | null>(null);
export function LocaleProvider({ locale, dictionary, products = [], children }: { locale: Locale; dictionary: Dictionary; products?: CmsProduct[]; children: React.ReactNode }) {
  return <LocaleContext.Provider value={{ locale, t: dictionary, products }}>{children}</LocaleContext.Provider>;
}
export function useLocale() {
  const value = useContext(LocaleContext);
  if (!value) throw new Error("LocaleProvider is required");
  return value;
}
