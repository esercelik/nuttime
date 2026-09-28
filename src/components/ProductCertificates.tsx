"use client";

import Image from "next/image";
import { useEffect, useRef, useState } from "react";
import { useLocale } from "@/i18n/LocaleProvider";

const certificates = [
  {
    language: "tr",
    src: "/nuttime/certificates/brcgs-food-safety-2026-tr.png",
  },
  {
    language: "en",
    src: "/nuttime/certificates/brcgs-food-safety-2026-en.png",
  },
  {
    language: "de",
    src: "/nuttime/certificates/brcgs-food-safety-2026-de.png",
  },
] as const;

type Certificate = (typeof certificates)[number];

export default function ProductCertificates() {
  const { locale, t } = useLocale();
  const copy = t.certificates;
  const [selected, setSelected] = useState<Certificate | null>(null);
  const dialogRef = useRef<HTMLDialogElement>(null);
  const validity = new Intl.DateTimeFormat(locale, {
    dateStyle: "long",
    timeZone: "UTC",
  }).format(new Date("2026-10-27T00:00:00Z"));

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!selected || !dialog) return;

    const previousOverflow = document.body.style.overflow;
    dialog.showModal();
    document.body.style.overflow = "hidden";

    return () => {
      dialog.close();
      document.body.style.overflow = previousOverflow;
    };
  }, [selected]);

  return (
    <section
      className="certificate-section"
      id="sertifikalar"
      aria-labelledby="certificates-title"
    >
      <header className="certificate-heading">
        <div>
          <p className="eyebrow">{copy.eyebrow}</p>
          <h2 id="certificates-title">{copy.heading}</h2>
        </div>
        <p>{copy.description}</p>
      </header>

      <dl className="certificate-facts">
        <div>
          <dt>{copy.issuer}</dt>
          <dd>TÜV Rheinland Cert GmbH</dd>
        </div>
        <div>
          <dt>{copy.number}</dt>
          <dd dir="ltr">01 183 2015879</dd>
        </div>
        <div>
          <dt>{copy.grade}</dt>
          <dd>AA</dd>
        </div>
        <div>
          <dt>{copy.validUntil}</dt>
          <dd>
            <time dateTime="2026-10-27">{validity}</time>
          </dd>
        </div>
      </dl>

      <div className="certificate-grid">
        {certificates.map((certificate) => {
          const language = copy.languages[certificate.language];

          return (
            <article className="certificate-card" key={certificate.language}>
              <button
                type="button"
                className="certificate-preview"
                aria-label={`${copy.preview}: ${language}`}
                aria-haspopup="dialog"
                onClick={() => setSelected(certificate)}
              >
                <Image
                  src={certificate.src}
                  alt={`BRCGS · ${language} · 01 183 2015879`}
                  width={992}
                  height={1403}
                  sizes="(max-width: 767px) 90vw, 30vw"
                />
                <span className="certificate-zoom" aria-hidden="true">
                  ↗
                </span>
              </button>
              <div className="certificate-card-heading">
                <div>
                  <p>BRCGS · Food Safety</p>
                  <h3>{language}</h3>
                </div>
                <span
                  className="certificate-language"
                  lang={certificate.language}
                >
                  {certificate.language.toUpperCase()}
                </span>
              </div>
              <div className="certificate-actions">
                <a
                  href={certificate.src}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  {copy.open}
                  <span aria-hidden="true">↗</span>
                </a>
                <a href={certificate.src} download>
                  {copy.download}
                  <span aria-hidden="true">↓</span>
                </a>
              </div>
            </article>
          );
        })}
      </div>

      <dialog
        ref={dialogRef}
        className="certificate-dialog"
        aria-labelledby="certificate-dialog-title"
        onClose={() => setSelected(null)}
        onClick={(event) => {
          if (event.target === event.currentTarget) dialogRef.current?.close();
        }}
      >
        {selected && (
          <>
            <div className="certificate-dialog-header">
              <h3 id="certificate-dialog-title">
                BRCGS · {copy.languages[selected.language]}
              </h3>
              <button
                type="button"
                onClick={() => dialogRef.current?.close()}
                aria-label={copy.close}
                autoFocus
              >
                ×
              </button>
            </div>
            <div className="certificate-dialog-body">
              <Image
                src={selected.src}
                alt={`BRCGS · ${copy.languages[selected.language]} · 01 183 2015879`}
                width={992}
                height={1403}
                unoptimized
              />
            </div>
          </>
        )}
      </dialog>
    </section>
  );
}
