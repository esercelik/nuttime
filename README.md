# Nuttime V2

Ana proje Next.js / React Three Fiber 3D sitesidir. Laravel / Filament admin paneli tüm modülleriyle backend/ altında bulunur. Eski V1 sitesi GitHub'daki v1 dalında korunur.

## Yerel geliştirme

Node.js 22 ve PHP 8.5 gereklidir.

```sh
npm ci
cp .env.example .env.local
cp backend/.env.example backend/.env
cd backend
composer install --no-interaction
php artisan key:generate
php artisan migrate --no-interaction
php artisan storage:link --no-interaction
npm ci && npm run build
php artisan nuttime:import-v2-content --no-interaction
# İlk kurulumda, mevcut hesabınız yoksa:
php artisan make:filament-user
cd ..
npm run dev
```

Site: http://localhost:3010 — Admin: http://localhost:3010/admin

Admin mevcut User hesaplarını kullanır. V2 metinleri Sayfa Bölümleri ekranında page_key=v2 kayıtlarından yönetilir. Ürün adları ve içerik/alerjen bilgileri Ürünler ekranından gelir. Aktif olmayan ürünler CMS API'sinde yayınlanmaz. backend/ ayrı bir PHP sunucusu gerektirir; dağıtımda NUTTIME_BACKEND_URL bu sunucuya ayarlanmalıdır. Veritabanı, yüklenen dosyalar ve .env dosyaları Git'e eklenmez.

## Kontroller

```sh
npx tsc --noEmit
npm run build
cd backend && php artisan test --compact
```
