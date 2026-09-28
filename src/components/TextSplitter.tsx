import clsx from "clsx";

type Props = {
  text: string;
  className?: string;
  wordDisplayStyle?: "inline-block" | "block";
};

export function TextSplitter({
  text,
  className,
  wordDisplayStyle = "inline-block",
}: Props) {
  if (!text) return null;

  const words = text.split(" ");

  return words.map((word: string, wordIndex: number) => {
    // Arabic joining requires a complete word; never split connected glyphs.
    const splitText = /[\u0600-\u06ff]/u.test(word) ? [word] : Array.from(word);
    return (
      <span
        aria-hidden="true"
        className={clsx("split-word", className)}
        style={{ display: wordDisplayStyle, whiteSpace: "pre" }}
        key={`${wordIndex}-${word}`}
      >
        {splitText.map((char, charIndex) => {
          if (char === " ") return ` `;
          return (
            <span
              key={charIndex}
              className={`split-char inline-block split-char--${wordIndex}-${charIndex}`}
            >
              {char}
            </span>
          );
        })}
        {wordIndex < words.length - 1 ? (
          <span className="split-char">{` `}</span>
        ) : (
          ""
        )}
      </span>
    );
  });
}
