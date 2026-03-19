<?php

namespace Modules\Webhook\Application\Services;

use App\Core\Support\Query\ApiQueryParams;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Webhook\Application\Contracts\WebhookServiceInterface;
use Modules\Webhook\Domain\Models\Webhook;
use Modules\Webhook\Infrastructure\Contracts\WebhookRepositoryInterface;

final class WebhookService implements WebhookServiceInterface
{
    public function __construct(
        private readonly WebhookRepositoryInterface $webhooks,
    ) {}

    public function paginateForUser(int $userId, ApiQueryParams $params): LengthAwarePaginator
    {
        return $this->webhooks->paginateForUser($userId, $params);
    }

    public function createForUser(int $userId, array $data): array
    {
        $authToken = null;
        $authSecret = null;

        $authType = (string) ($data['auth_type'] ?? 'none');
        if ($authType === 'token') {
            $authToken = $this->generateToken();
            $data['auth_token_hash'] = Hash::make($authToken);
            $data['auth_secret_encrypted'] = null;
        } elseif ($authType === 'hmac') {
            $authSecret = $this->generateSecret();
            $data['auth_secret_encrypted'] = Crypt::encryptString($authSecret);
            $data['auth_token_hash'] = null;
        } else {
            $data['auth_type'] = 'none';
            $data['auth_token_hash'] = null;
            $data['auth_secret_encrypted'] = null;
        }

        $data['user_id'] = $userId;
        $data['public_id'] = (string) Str::uuid();

        /** @var Webhook $webhook */
        $webhook = $this->webhooks->create($data);

        // auth_token/auth_secret chi tra ve 1 lan.
        return ['webhook' => $webhook, 'auth_token' => $authToken, 'auth_secret' => $authSecret];
    }

    public function updateForUser(int $userId, int $id, array $data): array
    {
        $webhook = $this->webhooks->findForUserOrFail($id, $userId);

        $authToken = null;
        $authSecret = null;

        $authType = array_key_exists('auth_type', $data)
            ? (string) $data['auth_type']
            : (string) $webhook->auth_type;

        $rotate = (bool) ($data['rotate_token'] ?? false);
        $rotateSecret = (bool) ($data['rotate_secret'] ?? false);

        if ($authType === 'none') {
            $data['auth_type'] = 'none';
            $data['auth_token_hash'] = null;
            $data['auth_secret_encrypted'] = null;
        } elseif ($authType === 'token') {
            $data['auth_type'] = 'token';
            $data['auth_secret_encrypted'] = null;

            // Neu chua co token hoac user yeu cau rotate -> tao moi.
            if ($rotate || !$webhook->auth_token_hash) {
                $authToken = $this->generateToken();
                $data['auth_token_hash'] = Hash::make($authToken);
            } else {
                unset($data['auth_token_hash']); // giu nguyen
            }
        } elseif ($authType === 'hmac') {
            $data['auth_type'] = 'hmac';
            $data['auth_token_hash'] = null;

            // Neu chua co secret hoac user yeu cau rotate -> tao moi.
            if ($rotateSecret || !$webhook->auth_secret_encrypted) {
                $authSecret = $this->generateSecret();
                $data['auth_secret_encrypted'] = Crypt::encryptString($authSecret);
            } else {
                unset($data['auth_secret_encrypted']); // giu nguyen
            }
        }

        unset($data['rotate_token'], $data['rotate_secret']); // field UI, khong luu DB

        $webhook = $this->webhooks->update($webhook, $data);

        return ['webhook' => $webhook, 'auth_token' => $authToken, 'auth_secret' => $authSecret];
    }

    public function getForUser(int $userId, int $id): Webhook
    {
        return $this->webhooks->findForUserOrFail($id, $userId);
    }

    public function deleteForUser(int $userId, int $id): void
    {
        $webhook = $this->webhooks->findForUserOrFail($id, $userId);
        $this->webhooks->delete($webhook);
    }

    public function rotateToken(int $userId, int $id): string
    {
        $webhook = $this->webhooks->findForUserOrFail($id, $userId);

        $authToken = $this->generateToken();

        $this->webhooks->update($webhook, [
            'auth_type' => 'token',
            'auth_token_hash' => Hash::make($authToken),
            'auth_secret_encrypted' => null,
        ]);

        return $authToken;
    }

    private function generateToken(): string
    {
        // Token plain chi tra ve 1 lan. DB chi luu hash.
        return Str::random(48);
    }

    private function generateSecret(): string
    {
        // Secret plain chi tra ve 1 lan. DB luu encrypt de verify signature.
        return Str::random(64);
    }
}
