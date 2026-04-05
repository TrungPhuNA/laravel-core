<?php

namespace Modules\Ecommerce\Support;

use Illuminate\Contracts\Container\BindingResolutionException;
use RuntimeException;

final class ShopResolver
{
    public static function context(): ShopContext
    {
        try {
            return app(ShopContext::class);
        } catch (BindingResolutionException $e) {
            throw new RuntimeException('Missing ShopContext. Ensure `ecm_shop` middleware is applied.', 0, $e);
        }
    }

    public static function id(): int
    {
        return self::context()->shopId;
    }
}

