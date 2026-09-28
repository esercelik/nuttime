"use client";
import Image from "next/image";
import ProductCertificates from "@/components/ProductCertificates";
import { useLocale } from "@/i18n/LocaleProvider";

const products = [
  { index: 0, slug: "antep-fistigi", image: "pistachio-spoon.webp", ratio: .42 },
  { index: 2, slug: "hindistan-cevizi", image: "coconut-spoon.webp", ratio: .42 },
  { index: 1, slug: "findik", image: "hazelnut.jpg", ratio: .45 },
  { index: 3, slug: "badem", image: "almond.jpg", ratio: .45 },
  { index: 4, slug: "yer-fistigi", image: "peanut.jpg", ratio: .52 },
];
const cmsSlugs = ["antep-fistikli-kremasi", "findik-kremasi", "hindistan-cevizi-ezmesi", "badem-ezmesi", "yer-fistigi-ezmesi"];
function IngredientText({ text }: { text: string }) {
  return <p>{text.split("**").map((part, index) => index % 2 ? <strong key={index}>{part}</strong> : part)}</p>;
}
export default function ProductDetails() {
  const { locale, t, products: cmsProducts } = useLocale();
  const percentage = new Intl.NumberFormat(locale, { style: "percent" });
  const unit = (value: number, name: string) => new Intl.NumberFormat(locale, { style: "unit", unit: name, unitDisplay: "short" }).format(value);
  const packageValues = [unit(250, "gram"), unit(400, "gram"), unit(74, "millimeter"), unit(85, "millimeter"), t.units];
  return <section id="icerik" className="product-collection">
    <div className="collection-heading"><div><p className="eyebrow">{t.insideEyebrow}</p><h2>{t.insideHeading}</h2></div><p>{t.collectionNote}</p></div>
    <nav className="collection-nav" aria-label={t.allFlavors}>{products.map(product => <a href={`#urun-${product.slug}`} key={product.slug}><span aria-hidden="true">0{product.index + 1}</span>{t.shortNames[product.index]}</a>)}</nav>
    <div className="collection-grid">
      {products.map((product, cardIndex) => <article className={cardIndex < 2 ? "product-card product-card-featured" : "product-card"} key={product.slug} id={`urun-${product.slug}`}>
        <div className="product-photo"><Image src={`/nuttime/${product.image}`} alt={`${t.productNames[product.index]} — ${cardIndex < 2 ? t.textureAlt : t.photoAlt}`} width={1707} height={2560} sizes={cardIndex < 2 ? "(max-width: 767px) 100vw, 50vw" : "(max-width: 767px) 100vw, 33vw"} /><span className="product-index" aria-hidden="true">0{product.index + 1}</span><span className="product-ratio">{percentage.format(product.ratio)} {t.shortNames[product.index]}</span></div>
        <div className="product-card-copy"><p className="eyebrow">{t.crunchy} <span aria-hidden="true">·</span> {unit(250, "gram")}</p><h3>{t.productNames[product.index]}</h3>
          {cmsProducts.find(item => item.source_slug === cmsSlugs[product.index])?.ingredients || cardIndex < 2 ? <details className="ingredient-panel"><summary>{t.ingredients}<span aria-hidden="true">+</span></summary><IngredientText text={[cmsProducts.find(item => item.source_slug === cmsSlugs[product.index])?.ingredients || t.ingredientTexts[cardIndex], cmsProducts.find(item => item.source_slug === cmsSlugs[product.index])?.allergen_information].filter(Boolean).join(" ")} /></details> : <><p className="product-description">{t.productBody}</p><a className="collection-link" href="#lezzetler">{t.backFlavors}</a></>}
          <a className="product-certificate-link" href="#sertifikalar">{t.certificates.heading}<span aria-hidden="true">↗</span></a>
        </div>
      </article>)}
    </div>
    <section className="packaging-panel" aria-labelledby="packaging-title"><p className="eyebrow" id="packaging-title">{t.packaging}</p><dl>{t.packageLabels.map((label, index) => <div key={label}><dt>{label}</dt><dd>{packageValues[index]}</dd></div>)}</dl></section>
    <ProductCertificates />
  </section>;
}
