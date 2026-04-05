<?php

namespace Modules\Ecommerce\Application\Services;

use App\Core\Exceptions\ApiException;
use App\Core\Exceptions\ErrorCode;
use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Ecommerce\Application\Contracts\CustomerServiceInterface;
use Modules\Ecommerce\Domain\Models\Customer;
use Modules\Ecommerce\Infrastructure\Contracts\CustomerRepositoryInterface;
use Modules\Ecommerce\Support\ShopResolver;

final class CustomerService implements CustomerServiceInterface
{
    public function __construct(
        private readonly CustomerRepositoryInterface $customers,
    ) {}

    public function paginate(ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->customers->paginate($params);
    }

    public function getById(int $id): Customer
    {
        return $this->customers->findOrFail($id);
    }

    public function create(array $input): Customer
    {
        $input['shop_id'] = ShopResolver::id();
        $input = $this->normalize($input);

        if (isset($input['email']) && $input['email'] !== null) {
            if ($this->customers->existsByEmail((string) $input['email'])) {
                throw ApiException::unprocessable(
                    ErrorCode::VALIDATION_ERROR->value,
                    __('messages.validation_error'),
                    ['email' => ['Email already exists']],
                );
            }
        }

        return DB::transaction(fn () => $this->customers->create($input));
    }

    public function update(int $id, array $input): Customer
    {
        $customer = $this->customers->findOrFail($id);

        $input = $this->normalize($input, partial: true);

        if (array_key_exists('email', $input) && $input['email'] !== null) {
            $email = (string) $input['email'];
            if ($this->customers->existsByEmail($email, exceptId: $customer->id)) {
                throw ApiException::unprocessable(
                    ErrorCode::VALIDATION_ERROR->value,
                    __('messages.validation_error'),
                    ['email' => ['Email already exists']],
                );
            }
        }

        return DB::transaction(fn () => $this->customers->update($customer, $input));
    }

    public function delete(int $id): void
    {
        $customer = $this->customers->findOrFail($id);
        DB::transaction(fn () => $this->customers->delete($customer));
    }

    private function normalize(array $input, bool $partial = false): array
    {
        if (!$partial || array_key_exists('name', $input)) {
            $name = $input['name'] ?? null;
            $name = is_string($name) ? trim($name) : $name;
            $input['name'] = $name === '' ? null : $name;
        }

        if (!$partial || array_key_exists('email', $input)) {
            $email = $input['email'] ?? null;
            $email = is_string($email) ? Str::lower(trim($email)) : $email;
            $input['email'] = $email === '' ? null : $email;
        }

        if (!$partial || array_key_exists('phone', $input)) {
            $phone = $input['phone'] ?? null;
            $phone = is_string($phone) ? trim($phone) : $phone;
            $input['phone'] = $phone === '' ? null : $phone;
        }

        if (array_key_exists('note', $input)) {
            $note = $input['note'];
            $note = is_string($note) ? trim($note) : $note;
            $input['note'] = $note === '' ? null : $note;
        }

        return $input;
    }
}
