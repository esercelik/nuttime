import tr from "./messages/tr.json";
import en from "./messages/en.json";
import de from "./messages/de.json";
import fr from "./messages/fr.json";
import es from "./messages/es.json";
import it from "./messages/it.json";
import ru from "./messages/ru.json";
import ar from "./messages/ar.json";
import zh from "./messages/zh.json";
import pt from "./messages/pt.json";
import type { Locale } from "./config";
export type Dictionary = typeof en;
const dictionaries: Record<Locale, Dictionary> = { tr, en, de, fr, es, it, ru, ar, zh, pt };
export function getDictionary(locale: Locale): Dictionary { return dictionaries[locale]; }
