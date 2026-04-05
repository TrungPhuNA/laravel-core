<?php

namespace Modules\Ecommerce\Support;

use Modules\Ecommerce\Domain\Models\Shop;

final class ShopContext
{
    public function __construct(
        public readonly int $shopId,
        public readonly ?Shop $shop = null,
    ) {}
}

