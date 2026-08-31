# E-commerce readiness

The current catalog deliberately has no price, stock, cart, checkout, order, or customer-account logic. Products have stable IDs and optional unique SKUs, so future commerce modules can reference them without changing public URLs.

Recommended future modules are separate tables for product variants, price books and currencies, stock and warehouses, carts, customer accounts, addresses, orders, payment records, shipping, coupons and invoices. Keep the catalog and editorial controllers independent from those modules.
