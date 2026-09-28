import "server-only";
import { cache } from "react";
import { getDictionary, type Dictionary } from "@/i18n/dictionaries";
import type { Locale } from "@/i18n/config";
export type CmsProduct = { source_slug: string; name: string; description: string; ingredients: string | null; allergen_information: string | null; weight_grams: number | null; primary_ingredient_percentage: string | null };
export const getV2Content = cache(async (locale: Locale): Promise<{ dictionary: Dictionary; products: CmsProduct[] }> => {
  const dictionary = { ...getDictionary(locale) };
  try {
    const backend = (process.env.NUTTIME_BACKEND_URL || "http://127.0.0.1:8000").replace(/\/$/, "");
    const response = await fetch(backend + "/api/cms/v2/" + locale, { cache: "no-store", signal: AbortSignal.timeout(2500) });
    if (!response.ok) throw new Error("CMS response " + response.status);
    const data = await response.json();
    for (const key of Object.keys(dictionary)) {
      if (typeof dictionary[key as keyof Dictionary] === "string" && typeof data.copy?.[key] === "string") Object.assign(dictionary, { [key]: data.copy[key] });
    }
    const slugs = ["antep-fistikli-kremasi", "findik-kremasi", "hindistan-cevizi-ezmesi", "badem-ezmesi", "yer-fistigi-ezmesi"];
    const products: CmsProduct[] = Array.isArray(data.products) ? data.products : [];
    dictionary.productNames = dictionary.productNames.map((name, index) => products.find(product => product.source_slug === slugs[index])?.name || name);
    return { dictionary, products };
  } catch (error) {
    console.warn("Nuttime CMS unavailable; using existing content.", error instanceof Error ? error.message : error);
    return { dictionary, products: [] };
  }
});
