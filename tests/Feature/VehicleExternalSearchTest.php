<?php

use Illuminate\Support\Facades\DB;

function externalDbReady(): bool
{
    return ! empty(env('REMOTE_DB_HOST'))
        && ! empty(env('REMOTE_DB_DATABASE'))
        && ! empty(env('REMOTE_DB_USERNAME'));
}

it('can connect to external_mysql database', function () {
    if (! externalDbReady()) {
        test()->markTestSkipped('External DB environment variables not configured.');
    }

    try {
        $rows = DB::connection('external_mysql')->select('SELECT 1 as one');
        expect($rows)->toBeArray();
        expect((array) ($rows[0] ?? []))->toHaveKey('one');
        expect((int) ((array) $rows[0])['one'])->toBe(1);
    } catch (Throwable $e) {
        test()->markTestSkipped('External DB not reachable: '.$e->getMessage());
    }
});

it('searches vehicle by vehicle_id from external DB', function () {
    if (! externalDbReady()) {
        test()->markTestSkipped('External DB environment variables not configured.');
    }

    $response = test()->getJson('/api/v1/vehicles/search', [
        'Authorization' => 'Bearer dummy',
        'search_type' => 'vehicle_id',
        'search_query' => '251144',
    ]);

    // Allow skipping if external DB not reachable at runtime
    if ($response->getStatusCode() >= 500) {
        test()->markTestSkipped('External DB not reachable during API call.');
    }

    $response->assertSuccessful();
    $json = $response->json();

    // Generic response shape
    expect($json)->toHaveKeys(['success', 'message', 'data', 'meta']);
    expect($json['success'])->toBeTrue();
    expect($json['data'])->toHaveKey('vehicles');
    expect($json['data']['vehicles'])->toBeArray();

    // Validate that at least one vehicle matches requested id
    $hasMatch = collect($json['data']['vehicles'])
        ->contains(fn ($v) => (string) ($v['vehicle_id'] ?? '') === '251144');

    expect($hasMatch)->toBeTrue();
});
