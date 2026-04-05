<?php

namespace Modules\Ecommerce\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Ecommerce\Application\Contracts\OrderServiceInterface;
use Modules\Ecommerce\Domain\Models\Order;
use Modules\Ecommerce\Domain\Models\OrderItem;
use Modules\Ecommerce\Infrastructure\Contracts\OrderRepositoryInterface;
use Modules\Ecommerce\Infrastructure\Contracts\ProductRepositoryInterface;
use Modules\Ecommerce\Support\ShopResolver;

final class OrderService implements OrderServiceInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $orders,
        private readonly ProductRepositoryInterface $products,
    ) {}

    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->orders->paginate($params);
    }

    public function getById(int $id): Order
    {
        return $this->orders->findOrFail($id);
    }

    public function create(array $input): Order
    {
        $shopId = ShopResolver::id();
        $input['shop_id'] = $shopId;
        [$data, $items] = $this->normalize($input);

        return DB::transaction(function () use ($data, $items) {
            $order = $this->orders->create($data);

            $subtotal = 0.0;

            foreach ($items as $it) {
                $product = null;
                if ($it['product_id'] !== null) {
                    $product = $this->products->findOrFail((int) $it['product_id']);
                }

                $qty = (int) $it['quantity'];
                $unit = (float) $it['unit_price'];
                $lineTotal = $qty * $unit;

                $subtotal += $lineTotal;

                OrderItem::query()->create([
                    'shop_id' => (int) $order->shop_id,
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'variant_id' => $it['variant_id'] !== null ? (int) $it['variant_id'] : null,
                    'sku' => $it['sku'] ?: ($product?->sku),
                    'name' => $it['name'] ?: ($product?->name ?? 'Item'),
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'total_price' => $lineTotal,
                ]);
            }

            $order = $this->orders->update($order, [
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'placed_at' => $data['placed_at'] ?? now(),
            ]);

            return $order->load(['items', 'customer']);
        });
    }

    public function update(int $id, array $input): Order
    {
        $order = $this->orders->findOrFail($id);

        [$data, $items] = $this->normalize($input, partial: true);

        return DB::transaction(function () use ($order, $data, $items) {
            $order = $this->orders->update($order, $data);

            if ($items !== null) {
                // Replace items (MVP). Later can optimize with diff.
                OrderItem::query()->where('order_id', $order->id)->delete();

                $subtotal = 0.0;
                foreach ($items as $it) {
                    $product = null;
                    if ($it['product_id'] !== null) {
                        $product = $this->products->findOrFail((int) $it['product_id']);
                    }

                    $qty = (int) $it['quantity'];
                    $unit = (float) $it['unit_price'];
                    $lineTotal = $qty * $unit;
                    $subtotal += $lineTotal;

                    OrderItem::query()->create([
                        'shop_id' => (int) $order->shop_id,
                        'order_id' => $order->id,
                        'product_id' => $product?->id,
                        'variant_id' => $it['variant_id'] !== null ? (int) $it['variant_id'] : null,
                        'sku' => $it['sku'] ?: ($product?->sku),
                        'name' => $it['name'] ?: ($product?->name ?? 'Item'),
                        'quantity' => $qty,
                        'unit_price' => $unit,
                        'total_price' => $lineTotal,
                    ]);
                }

                $order = $this->orders->update($order, [
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ]);
            }

            return $order->load(['items', 'customer']);
        });
    }

    public function delete(int $id): void
    {
        $order = $this->orders->findOrFail($id);
        DB::transaction(fn () => $this->orders->delete($order));
    }

    /**
     * @return array{0: array<string, mixed>, 1: list<array<string, mixed>>|null}
     */
    private function normalize(array $input, bool $partial = false): array
    {
        $items = null;

        if (array_key_exists('items', $input)) {
            $raw = $input['items'];
            if (!is_array($raw)) {
                $items = [];
            } else {
                $items = [];
                foreach ($raw as $it) {
                    if (!is_array($it)) {
                        continue;
                    }
                    $items[] = [
                        'product_id' => array_key_exists('product_id', $it) ? ($it['product_id'] !== null ? (int) $it['product_id'] : null) : null,
                        'variant_id' => array_key_exists('variant_id', $it) ? ($it['variant_id'] !== null ? (int) $it['variant_id'] : null) : null,
                        'sku' => isset($it['sku']) ? trim((string) $it['sku']) : null,
                        'name' => isset($it['name']) ? trim((string) $it['name']) : null,
                        'quantity' => (int) ($it['quantity'] ?? 1),
                        'unit_price' => (float) ($it['unit_price'] ?? 0),
                    ];
                }
            }
            unset($input['items']);
        }

        if (!$partial || array_key_exists('code', $input)) {
            if (!isset($input['code']) || trim((string) $input['code']) === '') {
                $input['code'] = $this->generateCode();
            } else {
                $input['code'] = Str::upper(trim((string) $input['code']));
            }
        }

        if (array_key_exists('customer_id', $input)) {
            $input['customer_id'] = $input['customer_id'] !== null ? (int) $input['customer_id'] : null;
        }

        foreach (['status', 'payment_status', 'fulfillment_status', 'currency'] as $k) {
            if (!$partial || array_key_exists($k, $input)) {
                if (isset($input[$k])) {
                    $input[$k] = Str::upper(trim((string) $input[$k]));
                }
            }
        }

        foreach (['customer_email', 'customer_phone', 'note'] as $k) {
            if (array_key_exists($k, $input)) {
                $v = $input[$k];
                $v = is_string($v) ? trim($v) : $v;
                $input[$k] = $v === '' ? null : $v;
            }
        }

        foreach (['shipping_address', 'billing_address'] as $k) {
            if (array_key_exists($k, $input)) {
                $input[$k] = is_array($input[$k]) ? $input[$k] : null;
            }
        }

        foreach (['placed_at', 'paid_at', 'cancelled_at'] as $k) {
            if (array_key_exists($k, $input) && $input[$k] === null) {
                $input[$k] = null;
            }
        }

        return [$input, $items];
    }

    private function generateCode(): string
    {
        $shopId = ShopResolver::id();

        for ($i = 0; $i < 5; $i++) {
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
