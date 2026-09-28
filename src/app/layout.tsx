import type { Metadata } from "next";
import localFont from "next/font/local";




import "./app.css";
import Header from "@/components/Header";
import ViewCanvas from "@/components/ViewCanvas";
import Footer from "@/components/Footer";
import { requestLocale } from "@/i18n/request";
import { getV2Content } from "@/lib/cms";
import { LocaleProvider } from "@/i18n/LocaleProvider";
import { locales, localeCodes, localePath } from "@/i18n/config";

export const dynamic = "force-dynamic";

export async function generateMetadata(): Promise<Metadata> {
  const { dictionary: t } = await getV2Content(requestLocale());
  return { title: t.metaTitle, description: t.metaDescription };
}

const alpino = localFont({
  src: "../../public/fonts/Alpino-Variable.woff2",
  display: "swap",
  weight: "100 900",
  variable: "--font-alpino",
});

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const locale = requestLocale();
  const { dictionary, products } = await getV2Content(locale);
  return (
    <html lang={locale} dir={locales[locale].direction} className={alpino.variable}>
      <head>{localeCodes.map(code => <link key={code} rel="alternate" hrefLang={code} href={localePath(code)} />)}<link rel="alternate" hrefLang="x-default" href="/" /></head>
      <body suppressHydrationWarning className="overflow-x-hidden bg-[#f4f0e4]">
        <LocaleProvider locale={locale} dictionary={dictionary} products={products}>
        <Header />
        <main id="main-content" tabIndex={-1}>
          {children}
          <ViewCanvas />
        </main>
        <Footer />
        </LocaleProvider>
      </body>

    </html>
  );
}
