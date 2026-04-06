<?php

namespace Modules\Ecommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Ecommerce\Domain\Models\Category;
use Modules\Ecommerce\Domain\Models\Customer;
use Modules\Ecommerce\Domain\Models\Order;
use Modules\Ecommerce\Domain\Models\OrderItem;
use Modules\Ecommerce\Domain\Models\Product;
use Modules\Ecommerce\Domain\Models\Shop;

final class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $shops = Shop::query()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        if ($shops->isEmpty()) {
            return;
        }

        $reset = (bool) env('ECM_DEMO_SEED_RESET', false);
        $customersCount = max(0, (int) env('ECM_DEMO_CUSTOMERS', 30));
        $productsCount = max(0, (int) env('ECM_DEMO_PRODUCTS', 40));
        $ordersCount = max(0, (int) env('ECM_DEMO_ORDERS', 60));

        $faker = \Faker\Factory::create('vi_VN');

        foreach ($shops as $shop) {
            DB::transaction(function () use ($shop, $reset, $customersCount, $productsCount, $ordersCount, $faker) {
                $shopId = (int) $shop->id;

                if ($reset) {
                    // Xoá theo thứ tự để tránh lỗi FK.
                    OrderItem::query()->where('shop_id', $shopId)->delete();
                    Order::query()->where('shop_id', $shopId)->delete();
                    DB::table('ecm_category_product')->where('shop_id', $shopId)->delete();
                    Product::query()->where('shop_id', $shopId)->forceDelete();
                    Category::query()->where('shop_id', $shopId)->forceDelete();
                    Customer::query()->where('shop_id', $shopId)->forceDelete();
                }

                $categories = $this->seedCategories($shopId);
                $products = $this->seedProducts($shopId, $productsCount, $categories, $faker);
                $customers = $this->seedCustomers($shopId, $customersCount, $faker);
                $this->seedOrders($shopId, $ordersCount, $customers, $products, $faker);
            });
        }
    }

    /**
     * @return list<Category>
     */
    private function seedCategories(int $shopId): array
    {
        $defs = [
            ['name' => 'Áo nam', 'slug' => 'ao-nam', 'position' => 10],
            ['name' => 'Áo nữ', 'slug' => 'ao-nu', 'position' => 20],
            ['name' => 'Quần', 'slug' => 'quan', 'position' => 30],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'position' => 40],
            ['name' => 'Sale', 'slug' => 'sale', 'position' => 50],
        ];

        $out = [];
        foreach ($defs as $d) {
            /** @var Category $cat */
            $cat = Category::withTrashed()->updateOrCreate(
                ['shop_id' => $shopId, 'slug' => $d['slug']],
                [
                    'parent_id' => null,
                    'name' => $d['name'],
                    'description' => null,
                    'position' => (int) $d['position'],
                    'is_active' => true,
                ],
            );

            if ($cat->trashed()) {
                $cat->restore();
            }

            $out[] = $cat->refresh();
        }

        return $out;
    }

    /**
     * @param list<Category> $categories
     * @return list<Product>
     */
    private function seedProducts(int $shopId, int $count, array $categories, \Faker\Generator $faker): array
    {
        $out = [];

        for ($i = 1; $i <= $count; $i++) {
            $sku = 'SKU'.str_pad((string) $i, 5, '0', STR_PAD_LEFT);
            $name = $faker->words(nb: 3, asText: true);
            $slug = Str::slug($name).'-'.$i;

            $price = (float) $faker->numberBetween(50_000, 1_500_000);
            $compare = $faker->boolean(35) ? ($price + (float) $faker->numberBetween(10_000, 200_000)) : null;

            /** @var Product $product */
            $product = Product::withTrashed()->updateOrCreate(
                ['shop_id' => $shopId, 'sku' => $sku],
                [
                    'name' => Str::title($name),
                    'slug' => $slug,
                    'description' => $faker->boolean(60) ? $faker->sentence(12) : null,
                    'price' => $price,
                    'compare_at_price' => $compare,
                    'cost_price' => $faker->boolean(50) ? ($price * 0.6) : null,
                    'currency' => 'VND',
                    'stock_qty' => $faker->numberBetween(0, 200),
                    'track_inventory' => true,
                    'allow_backorder' => false,
                    'is_active' => true,
                    'barcode' => $faker->boolean(25) ? (string) $faker->ean13() : null,
                    'weight' => $faker->boolean(40) ? $faker->randomFloat(3, 0.1, 2.5) : null,
                    'length' => null,
                    'width' => null,
                    'height' => null,
                    'meta' => [
                        'brand' => $faker->boolean(50) ? $faker->company() : null,
                    ],
                ],
            );

            if ($product->trashed()) {
                $product->restore();
            }

            $product = $product->refresh();

            // Attach 1-3 categories
            $categoryIds = collect($categories)
                ->shuffle()
                ->take($faker->numberBetween(1, min(3, max(1, count($categories)))))
                ->map(fn (Category $c) => (int) $c->id)
                ->values()
                ->all();

            $pivot = [];
            foreach ($categoryIds as $cid) {
                $pivot[$cid] = ['shop_id' => $shopId];
            }
            $product->categories()->sync($pivot);

            $out[] = $product;
        }

        return $out;
    }

    /**
     * @return list<Customer>
     */
    private function seedCustomers(int $shopId, int $count, \Faker\Generator $faker): array
    {
        $out = [];

        for ($i = 1; $i <= $count; $i++) {
            $code = 'CUST'.str_pad((string) $i, 5, '0', STR_PAD_LEFT);
            $email = "customer{$i}.shop{$shopId}@example.test";

            /** @var Customer $c */
            $c = Customer::withTrashed()->updateOrCreate(
                ['shop_id' => $shopId, 'code' => $code],
                [
                    'name' => $faker->name(),
                    'email' => $email,
                    'phone' => $faker->boolean(90) ? $faker->phoneNumber() : null,
                    'gender' => $faker->randomElement(['MALE', 'FEMALE', null]),
                    'birthday' => $faker->boolean(40) ? $faker->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d') : null,
                    'tags' => $faker->boolean(30) ? ['demo'] : null,
                    'note' => $faker->boolean(15) ? $faker->sentence(8) : null,
                ],
            );

            if ($c->trashed()) {
                $c->restore();
            }

            $out[] = $c->refresh();
        }

        return $out;
    }

    /**
     * @param list<Customer> $customers
     * @param list<Product> $products
     */
    private function seedOrders(int $shopId, int $count, array $customers, array $products, \Faker\Generator $faker): void
    {
        if ($customers === [] || $products === []) {
            return;
        }

        for ($i = 0; $i < $count; $i++) {
            /** @var Customer $customer */
            $customer = $customers[array_rand($customers)];

            $code = $this->generateOrderCode($shopId);

            $status = $faker->randomElement(['NEW', 'PROCESSING', 'COMPLETED', 'CANCELLED']);
            $paymentStatus = $status === 'CANCELLED'
                ? 'UNPAID'
                : $faker->randomElement(['UNPAID', 'PAID']);
            $fulfillmentStatus = $status === 'COMPLETED'
                ? 'FULFILLED'
                : ($status === 'CANCELLED' ? 'CANCELLED' : $faker->randomElement(['UNFULFILLED', 'PARTIAL']));

            /** @var Order $order */
            $order = Order::query()->create([
                'shop_id' => $shopId,
                'code' => $code,
                'customer_id' => $customer->id,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'fulfillment_status' => $fulfillmentStatus,
                'subtotal' => 0,
                'discount_total' => 0,
                'tax_total' => 0,
                'shipping_total' => 0,
                'total' => 0,
                'currency' => 'VND',
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'customer_snapshot' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                ],
                'shipping_address' => [
                    'name' => $customer->name,
                    'phone' => $customer->phone,
                    'line1' => $faker->streetAddress(),
                    'city' => $faker->city(),
                    'country' => 'VN',
                ],
                'billing_address' => null,
                'payment_method' => $paymentStatus === 'PAID' ? $faker->randomElement(['BANK_TRANSFER', 'COD']) : null,
                'shipping_method' => $faker->randomElement(['STANDARD', 'EXPRESS']),
                'shipping_provider' => $faker->randomElement(['GHN', 'GHTK', 'VTP', null]),
                'tracking_number' => $faker->boolean(30) ? Str::upper(Str::random(10)) : null,
                'note' => $faker->boolean(15) ? $faker->sentence(10) : null,
                'meta' => ['source' => $faker->randomElement(['web', 'admin', 'pos'])],
                'placed_at' => now()->subDays($faker->numberBetween(0, 30))->subMinutes($faker->numberBetween(0, 1200)),
                'paid_at' => $paymentStatus === 'PAID' ? now()->subDays($faker->numberBetween(0, 30)) : null,
                'cancelled_at' => $status === 'CANCELLED' ? now()->subDays($faker->numberBetween(0, 30)) : null,
            ]);

            $itemsCount = $faker->numberBetween(1, 4);
            $subtotal = 0.0;

            for ($j = 0; $j < $itemsCount; $j++) {
                /** @var Product $product */
                $product = $products[array_rand($products)];
                $qty = $faker->numberBetween(1, 3);
                $unit = (float) $product->price;
                $lineTotal = $qty * $unit;
                $subtotal += $lineTotal;

                OrderItem::query()->create([
                    'shop_id' => $shopId,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total_price' => $lineTotal,
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'meta' => null,
                ]);
            }

            $order->update([
                'subtotal' => $subtotal,
                'total' => $subtotal,
            ]);
        }
    }

    private function generateOrderCode(int $shopId): string
    {
        for ($i = 0; $i < 8; $i++) {
            $code = 'OD'.now()->format('ymd').Str::upper(Str::random(6));
            $exists = Order::query()
                ->where('shop_id', $shopId)
                ->where('code', $code)
                ->exists();
            if (!$exists) {
                return $code;
            }
        }

        return 'OD'.now()->format('ymdHis').Str::upper(Str::random(8));
    }
}

