import { NextRequest, NextResponse } from "next/server";
import { isLocale } from "./i18n/config";
export function middleware(request: NextRequest) {
  const segment = request.nextUrl.pathname.split("/")[1];
  const headers = new Headers(request.headers);
  headers.set("x-nuttime-locale", isLocale(segment) ? segment : "tr");
  return NextResponse.next({ request: { headers } });
}
export const config = { matcher: ["/((?!api|_next|.*\\.).*)"] };
