import { headers } from "next/headers";
import { isLocale, type Locale } from "./config";
export function requestLocale(): Locale {
  const locale = headers().get("x-nuttime-locale");
  return isLocale(locale) ? locale : "tr";
}
