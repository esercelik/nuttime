import { notFound, redirect } from "next/navigation";
import { isLocale } from "@/i18n/config";
import Storefront from "@/components/Storefront";
export default function Page({ params }: { params: { uid: string } }) {
  if (!isLocale(params.uid)) notFound();
  if (params.uid === "tr") redirect("/");
  return <Storefront locale={params.uid} />;
}
