# REQ-20260404-ecommerce-module

## Muc tieu

- Tạo module `Ecommerce` quản lý luồng bán hàng cơ bản cho shop online: danh mục/sản phẩm/khách hàng/đơn hàng.
- Hỗ trợ multi-shop (dùng chung core cho nhiều shop khác nhau): mọi bảng nghiệp vụ có `shop_id`, chọn shop qua header `X-Shop-Id`.
- Chuẩn hoá CSDL với prefix `ecm_`, API v1 theo `ApiResponse`, và admin UI React (CRUD).

## Pham vi

- In-scope:
  - CSDL + migrations cho các bảng cốt lõi (prefix `ecm_`).
  - API quản trị (ADMIN/SYSTEM + RBAC permissions): CRUD categories/products/customers, quản lý orders.
  - Admin SPA tại `/admin/ecommerce` (React + Vite): list/create/update/delete cho các entity trên.
- Out-of-scope:
  - Tích hợp cổng thanh toán thật (VNPay/MoMo/Stripe), webhook payment.
  - Tích hợp hãng vận chuyển, tính phí shipping động theo địa chỉ.
  - Hoàn tiền/đổi trả (refund/return), đa kho, khuyến mãi phức tạp (stacking rules).

## API

- Base: `/api/v1/ecm`
- Auth: `auth:sanctum` + `user_type:ADMIN,SYSTEM` + middleware `perm:*`

### Categories

- `GET /admin/categories` (perm: `ecommerce.categories.read`)
- `GET /admin/categories/{id}` (perm: `ecommerce.categories.read`)
- `POST /admin/categories` (perm: `ecommerce.categories.write`)
- `PUT /admin/categories/{id}` (perm: `ecommerce.categories.write`)
- `DELETE /admin/categories/{id}` (perm: `ecommerce.categories.delete`)

### Products

- `GET /admin/products` (perm: `ecommerce.products.read`)
- `GET /admin/products/{id}` (perm: `ecommerce.products.read`)
- `POST /admin/products` (perm: `ecommerce.products.write`)
- `PUT /admin/products/{id}` (perm: `ecommerce.products.write`)
- `DELETE /admin/products/{id}` (perm: `ecommerce.products.delete`)

### Customers

- `GET /admin/customers` (perm: `ecommerce.customers.read`)
- `GET /admin/customers/{id}` (perm: `ecommerce.customers.read`)
- `POST /admin/customers` (perm: `ecommerce.customers.write`)
- `PUT /admin/customers/{id}` (perm: `ecommerce.customers.write`)
- `DELETE /admin/customers/{id}` (perm: `ecommerce.customers.delete`)

### Orders

- `GET /admin/orders` (perm: `ecommerce.orders.read`)
- `GET /admin/orders/{id}` (perm: `ecommerce.orders.read`)
- `PUT /admin/orders/{id}` (perm: `ecommerce.orders.write`) (cập nhật trạng thái, thông tin đơn)
- `DELETE /admin/orders/{id}` (perm: `ecommerce.orders.delete`) (soft delete)

## Business rules

- `sku` và `slug` của product là unique (tránh trùng).
- `slug` của category là unique.
- Xoá category/product/customer là soft delete (giữ lịch sử).
- Order lưu snapshot thông tin khách + địa chỉ (JSON) để tránh thay đổi dữ liệu sau này làm sai lịch sử đơn.

## DB changes

### Migrations

- `ecm_shops`, `ecm_shop_users`
- `ecm_categories`
- `ecm_products`
- `ecm_product_variants`
- `ecm_product_images`
- `ecm_category_product` (pivot)
- `ecm_customers`
- `ecm_customer_addresses`
- `ecm_orders`
- `ecm_order_items`
- `ecm_payments`
- `ecm_shipments`
- `ecm_order_status_histories`
- `ecm_carts`, `ecm_cart_items`
- `ecm_inventory_movements`

### Columns (tóm tắt)

- `ecm_categories`: `parent_id`, `name`, `slug`, `description`, `position`, `is_active`, `deleted_at`, timestamps
- `ecm_products`: `sku`, `name`, `slug`, `description`, `price`, `compare_at_price`, `currency`, `stock_qty`, `is_active`, `deleted_at`, timestamps
- `ecm_customers`: `name`, `email`, `phone`, `note`, `deleted_at`, timestamps
- `ecm_customer_addresses`: `customer_id`, `label`, `name`, `phone`, `line1/line2`, `city`, `state`, `postal_code`, `country`, `is_default_shipping`, `is_default_billing`, timestamps
- `ecm_orders`: `code`, `customer_id`, `status`, `payment_status`, `fulfillment_status`, `subtotal`, `discount_total`, `tax_total`, `shipping_total`, `total`, `currency`,
  `customer_email`, `customer_phone`, `shipping_address`(json), `billing_address`(json), `note`, `placed_at`, `paid_at`, `cancelled_at`, `deleted_at`, timestamps
- `ecm_order_items`: `order_id`, `product_id`(nullable), `sku`, `name`, `quantity`, `unit_price`, `total_price`, timestamps

### Indexes

- Unique (theo shop): `shop_id+slug`, `shop_id+sku`, `shop_id+code`, ...
- Foreign keys: các `*_id` liên quan (cascade theo logic)
- Pivot unique: `(category_id, product_id)`

## Cache / Queue / Events (neu co)

- Chưa dùng trong MVP.

## Acceptance criteria

- [ ] Có module `Ecommerce` chạy được migrations, tạo đủ bảng prefix `ecm_`.
- [ ] API v1 trả đúng chuẩn `ApiResponse` và có middleware auth/perm.
- [ ] Admin UI `/admin/ecommerce` CRUD được categories/products/customers, và xem/cập nhật orders.

## Notes

- Khi cần mở rộng checkout/cart/payment/shipping thật, sẽ thêm `ecm_carts`, `ecm_cart_items`, `ecm_payments`, `ecm_shipments`, `ecm_coupons` theo phase 2.
