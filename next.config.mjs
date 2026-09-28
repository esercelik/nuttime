const backend = (process.env.NUTTIME_BACKEND_URL || "http://127.0.0.1:8000").replace(/\/$/, "");
/** @type {import('next').NextConfig} */
const nextConfig = {
  async rewrites() {
    return { beforeFiles: ["/admin/:path*", "/livewire/:path*", "/css/filament/:path*", "/js/filament/:path*", "/fonts/filament/:path*", "/storage/:path*", "/api/cms/:path*"].map(source => ({ source, destination: backend + source })), afterFiles: [], fallback: [] };
  },
};
export default nextConfig;
