<?php

declare(strict_types=1);

use Laravel\Mcp\Server\Tool;
use App\Mcp\Servers\TermiiServer;
use App\Mcp\Tools\GetBalanceTool;
use App\Mcp\Tools\SendMessageTool;
use App\Mcp\Tools\GetEsimUsageTool;
use App\Mcp\Tools\SendCampaignTool;
use App\Mcp\Tools\ListSenderIdsTool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use App\Mcp\Tools\PurchaseEsimPlanTool;
use App\Mcp\Tools\GetMessageHistoryTool;
use App\Mcp\Tools\ListEsimDataPlansTool;
use Illuminate\Http\Client\Request as ClientRequest;

function withTermiiKey(string $key = 'test-api-key'): void
{
    request()->headers->set('Authorization', 'Bearer '.$key);
}

it('returns the account balance when an API key is supplied', function () {
    Http::fake([
        'v4.api.termii.com/api/get-balance*' => Http::response([
            'user' => 'Demo',
            'balance' => 1200,
            'currency' => 'NGN',
        ], 200),
    ]);

    withTermiiKey();

    TermiiServer::tool(GetBalanceTool::class)
        ->assertOk()
        ->assertSee('NGN');
});

it('accepts an encrypted API key from the url query parameter', function () {
    Http::fake([
        'v4.api.termii.com/api/get-balance*' => Http::response(['balance' => 5, 'currency' => 'NGN'], 200),
    ]);

    request()->query->set('key', Crypt::encryptString('url-key'));

    TermiiServer::tool(GetBalanceTool::class)
        ->assertOk()
        ->assertSee('NGN');
});

it('rejects a url key that is not a valid encrypted token', function () {
    config()->set('termii.api_key', null);
    request()->query->set('key', 'not-a-real-token');

    TermiiServer::tool(GetBalanceTool::class)
        ->assertHasErrors();

    Http::assertNothingSent();
});

it('mints an encrypted connector url that only this server can decrypt', function () {
    $response = $this->postJson('/connect', ['key' => 'my-termii-key']);

    $response->assertOk()->assertJsonStructure(['token', 'url']);

    expect(Crypt::decryptString($response->json('token')))->toBe('my-termii-key');
    expect($response->json('url'))->toContain('/mcp?key=');
    expect($response->json('token'))->not->toContain('my-termii-key');
});

it('errors clearly when no API key is provided', function () {
    config()->set('termii.api_key', null);
    request()->headers->remove('Authorization');

    TermiiServer::tool(GetBalanceTool::class)
        ->assertHasErrors();

    Http::assertNothingSent();
});

it('sends an SMS with the expected payload and credentials', function () {
    Http::fake([
        'v4.api.termii.com/api/sms/send' => Http::response([
            'message_id' => 'abc-123',
            'message' => 'Successfully Sent',
        ], 200),
    ]);

    withTermiiKey('secret-key');

    TermiiServer::tool(SendMessageTool::class, [
        'to' => '2348012345678',
        'from' => 'Acme',
        'sms' => 'Hello there',
    ])->assertOk();

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'sms/send')
        && $request['to'] === '2348012345678'
        && $request['sms'] === 'Hello there'
        && $request['from'] === 'Acme'
        && $request['api_key'] === 'secret-key');
});

it('rejects sending a message without required fields', function () {
    withTermiiKey();

    TermiiServer::tool(SendMessageTool::class, [
        'from' => 'Acme',
    ])->assertHasErrors();
});

it('lists eSIM data plans after exchanging the API key for a bearer token', function () {
    Http::fake([
        'v4.api.termii.com/api/esim/authenticate' => Http::response(['token' => 'esim-token'], 200),
        'v4.api.termii.com/api/esim/data/plan/fetch*' => Http::response(['data' => []], 200),
    ]);

    withTermiiKey('secret-key');

    TermiiServer::tool(ListEsimDataPlansTool::class, [
        'country' => 'Nigeria',
        'type' => 'LOCAL',
    ])->assertOk();

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'esim/authenticate')
        && $request['api_key'] === 'secret-key');

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'esim/data/plan/fetch')
        && $request->header('X-Token') === ['esim-token']
        && $request['country'] === 'Nigeria'
        && $request['type'] === 'LOCAL');
});

it('purchases an eSIM data plan with the expected payload', function () {
    Http::fake([
        'v4.api.termii.com/api/esim/authenticate' => Http::response(['token' => 'esim-token'], 200),
        'v4.api.termii.com/api/esim/data-plan/purchase' => Http::response(['status' => 'success'], 200),
    ]);

    withTermiiKey();

    TermiiServer::tool(PurchaseEsimPlanTool::class, [
        'iccid' => '8944500987654321000',
        'product_id' => 'prod-1',
        'iso3' => 'NGA',
    ])->assertOk();

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'esim/data-plan/purchase')
        && $request->header('X-Token') === ['esim-token']
        && $request['iccid'] === '8944500987654321000'
        && $request['productId'] === 'prod-1'
        && $request['iso3'] === 'NGA');
});

it('errors when the eSIM token exchange fails', function () {
    Http::fake([
        'v4.api.termii.com/api/esim/authenticate' => Http::response(['message' => 'invalid key'], 200),
    ]);

    withTermiiKey();

    TermiiServer::tool(GetEsimUsageTool::class, [
        'iccid' => '8944500987654321000',
    ])->assertHasErrors();
});

it('filters Sender IDs by name and status', function () {
    Http::fake([
        'v4.api.termii.com/api/sender-id*' => Http::response(['data' => []], 200),
    ]);

    withTermiiKey();

    TermiiServer::tool(ListSenderIdsTool::class, [
        'name' => 'Acme',
        'status' => 'active',
    ])->assertOk();

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'sender-id')
        && $request['name'] === 'Acme'
        && $request['status'] === 'active');
});

it('fetches the report for a single message by id', function () {
    Http::fake([
        'v4.api.termii.com/api/sms/inbox*' => Http::response(['data' => []], 200),
    ]);

    withTermiiKey();

    TermiiServer::tool(GetMessageHistoryTool::class, [
        'message_id' => 'msg-42',
    ])->assertOk();

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'sms/inbox')
        && $request['message_id'] === 'msg-42');
});

it('sends a campaign with campaign type and scheduling fields', function () {
    Http::fake([
        'v4.api.termii.com/api/sms/campaigns/send' => Http::response(['message' => 'Campaign sent'], 200),
    ]);

    withTermiiKey();

    TermiiServer::tool(SendCampaignTool::class, [
        'country_code' => '234',
        'sender_id' => 'Acme',
        'message' => 'Big sale tomorrow',
        'phonebook_id' => 'pb-1',
        'campaign_type' => 'personalized',
        'schedule_sms_status' => 'scheduled',
        'schedule_time' => '2026-07-10 15:00',
    ])->assertOk();

    Http::assertSent(fn (ClientRequest $request) => str_contains($request->url(), 'sms/campaigns/send')
        && $request['campaign_type'] === 'personalized'
        && $request['schedule_sms_status'] === 'scheduled'
        && $request['schedule_time'] === '2026-07-10 15:00');
});

it('rejects a scheduled campaign without a schedule time', function () {
    withTermiiKey();

    TermiiServer::tool(SendCampaignTool::class, [
        'country_code' => '234',
        'sender_id' => 'Acme',
        'message' => 'Big sale tomorrow',
        'phonebook_id' => 'pb-1',
        'schedule_sms_status' => 'scheduled',
    ])->assertHasErrors();

    Http::assertNothingSent();
});

arch('every MCP tool extends the base Tool class')
    ->expect('App\Mcp\Tools')
    ->toExtend(Tool::class)
    ->ignoring('App\Mcp\Tools\Concerns');
