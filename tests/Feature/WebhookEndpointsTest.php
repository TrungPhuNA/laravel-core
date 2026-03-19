<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\Sanctum;
use Modules\Webhook\Domain\Models\WebhookRequest;
use Tests\TestCase;

final class WebhookEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_webhook_with_token_auth(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $res = $this->postJson('/api/v1/webhooks', [
            'name' => 'Payment callback',
            'auth_type' => 'token',
            'allowed_methods' => ['POST'],
            'validation_rules' => [
                'email' => 'required|email',
            ],
        ]);

        $res->assertStatus(201);
        $res->assertJsonPath('status', 'success');
        $res->assertJsonPath('code', 'WEBHOOK_CREATE_SUCCESS');
        $this->assertNotEmpty((string) $res->json('data.webhook.public_id'));
        $this->assertNotEmpty((string) $res->json('data.auth_token'));
        $this->assertStringContainsString('/api/v1/webhooks/receive/', (string) $res->json('data.receive_url'));
    }

    public function test_receiver_requires_token_when_auth_type_token(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/v1/webhooks', [
            'name' => 'Payment callback',
            'auth_type' => 'token',
            'allowed_methods' => ['POST'],
            'validation_rules' => [
                'email' => 'required|email',
            ],
        ])->json('data');

        $publicId = (string) ($created['webhook']['public_id'] ?? '');
        $token = (string) ($created['auth_token'] ?? '');

        $unauth = $this->postJson("/api/v1/webhooks/receive/{$publicId}", [
            'email' => 'a@b.com',
        ]);
        $unauth->assertStatus(401);
        $unauth->assertJsonPath('status', 'fail');
        $unauth->assertJsonPath('code', 'UNAUTHORIZED');

        $ok = $this->withHeader('X-Webhook-Token', $token)->postJson("/api/v1/webhooks/receive/{$publicId}", [
            'email' => 'a@b.com',
        ]);
        $ok->assertOk();
        $ok->assertJsonPath('status', 'success');
        $ok->assertJsonPath('code', 'WEBHOOK_RECEIVED');
        $ok->assertJsonPath('data.validated.email', 'a@b.com');
    }

    public function test_receiver_rejects_not_allowed_method(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/v1/webhooks', [
            'name' => 'Only GET',
            'auth_type' => 'none',
            'allowed_methods' => ['GET'],
        ])->json('data');

        $publicId = (string) ($created['webhook']['public_id'] ?? '');

        $res = $this->postJson("/api/v1/webhooks/receive/{$publicId}", ['a' => 1]);
        $res->assertStatus(405);
        $res->assertJsonPath('status', 'fail');
        $res->assertJsonPath('code', 'METHOD_NOT_ALLOWED');
    }

    public function test_receiver_validates_payload_rules(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/v1/webhooks', [
            'name' => 'Validate order_id',
            'auth_type' => 'none',
            'allowed_methods' => ['POST'],
            'validation_rules' => [
                'order_id' => 'required|string|max:50',
            ],
        ])->json('data');

        $publicId = (string) ($created['webhook']['public_id'] ?? '');

        $res = $this->postJson("/api/v1/webhooks/receive/{$publicId}", []);
        // Core template quy uoc ValidationException -> status 400 (JSend fail).
        $res->assertStatus(400);
        $res->assertJsonPath('status', 'fail');
        $res->assertJsonPath('code', 'VALIDATION_ERROR');
        $this->assertNotEmpty((array) $res->json('data.order_id'));
    }

    public function test_user_can_list_show_and_prune_webhook_request_logs(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/v1/webhooks', [
            'name' => 'Logs demo',
            'auth_type' => 'none',
            'allowed_methods' => ['POST'],
        ])->json('data');

        $webhookId = (int) ($created['webhook']['id'] ?? 0);
        $publicId = (string) ($created['webhook']['public_id'] ?? '');

        // Hit receiver twice to create logs.
        $this->postJson("/api/v1/webhooks/receive/{$publicId}", ['a' => 1])->assertOk();
        $this->postJson("/api/v1/webhooks/receive/{$publicId}", ['b' => 2])->assertOk();

        $list = $this->getJson("/api/v1/webhooks/{$webhookId}/requests?per_page=10");
        $list->assertOk();
        $list->assertJsonPath('status', 'success');
        $items = (array) $list->json('data.items');
        $this->assertGreaterThanOrEqual(2, count($items));

        $firstId = (int) ($items[0]['id'] ?? 0);
        $show = $this->getJson("/api/v1/webhooks/{$webhookId}/requests/{$firstId}");
        $show->assertOk();
        $show->assertJsonPath('status', 'success');
        $this->assertNotEmpty((string) $show->json('data.request.body'));

        // Make one log old then prune by days.
        /** @var WebhookRequest $old */
        $old = WebhookRequest::query()->where('webhook_id', $webhookId)->orderBy('id', 'asc')->firstOrFail();
        $old->forceFill(['received_at' => Carbon::now()->subDays(60)])->save();

        $prune = $this->postJson("/api/v1/webhooks/{$webhookId}/requests/prune", ['days' => 30]);
        $prune->assertOk();
        $prune->assertJsonPath('status', 'success');
        $this->assertGreaterThanOrEqual(1, (int) $prune->json('data.deleted'));
    }

    public function test_receiver_accepts_hmac_signature(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $created = $this->postJson('/api/v1/webhooks', [
            'name' => 'HMAC demo',
            'auth_type' => 'hmac',
            'allowed_methods' => ['POST'],
        ])->json('data');

        $publicId = (string) ($created['webhook']['public_id'] ?? '');
        $secret = (string) ($created['auth_secret'] ?? '');
        $this->assertNotEmpty($secret);

        $ts = Carbon::now()->timestamp;
        $path = "/api/v1/webhooks/receive/{$publicId}";
        $body = '{"email":"a@b.com","amount":120000}';
        $canonical = $ts."\nPOST\n".$path."\n\n".$body;
        $sig = hash_hmac('sha256', $canonical, $secret);

        // Call with raw JSON body so signature matches exactly.
        $res = $this->call(
            'POST',
            $path,
            [], // parameters
            [], // cookies
            [], // files
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_WEBHOOK_TIMESTAMP' => (string) $ts,
                'HTTP_X_WEBHOOK_SIGNATURE' => 'sha256='.$sig,
                'HTTP_ACCEPT' => 'application/json',
            ],
            $body
        );

        $res->assertOk();
        $res->assertJsonPath('status', 'success');
        $res->assertJsonPath('code', 'WEBHOOK_RECEIVED');
    }
}
