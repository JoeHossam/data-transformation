<?php

namespace Tests\Unit;

use App\Exceptions\TransformationException;
use App\Models\Claim;
use App\Models\MappingRule;
use App\Models\Payer;
use App\Services\DataTransformerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DataTransformerServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DataTransformerService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DataTransformerService::class);
    }

    public function testTransformWithValidData(): void
    {
        $claim = $this->createClaim();
        $this->createMappingRules();

        $result = $this->service->transform($claim->id, 22);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('claimReference', $result);
        $this->assertArrayHasKey('payer', $result);
        $this->assertArrayHasKey('claimStatuses', $result);
    }

    public function testTransformWithMissingSchema(): void
    {
        $claim = $this->createClaim();

        $this->expectException(TransformationException::class);
        $this->expectExceptionMessage('No schema found for endpoint 22');
        $this->service->transform($claim->id, 22);
    }

    public function testCachingOfInternalFields(): void
    {
        $claim = $this->createClaim();
        $this->createMappingRules();
        $endpointID = 22;

        $this->service->transform($claim->id, $endpointID);
        $cacheKey = "transformed_data_{$claim->id}_{$endpointID}";
        $this->assertTrue(Cache::has($cacheKey));
    }

    protected function createClaim(array $overrides = []): Claim
    {
        $payer = Payer::factory()->create();
        return Claim::factory()->create(array_merge([
            'payer_id' => $payer->id
        ], $overrides));
    }

    protected function createMappingRules(array $rules = []): void
    {
        $defaultRules = [
            ['internal_field' => 'reference', 'external_field' => 'claimReference', 'data_type' => 'attribute'],
            ['internal_field' => null, 'external_field' => 'payer', 'data_type' => 'object'],
            ['internal_field' => 'payer.name', 'external_field' => 'payerName', 'data_type' => 'attribute', 'parent_id' => 2],
            ['internal_field' => 'claim_statuses', 'external_field' => 'claimStatuses', 'data_type' => 'object_list'],
            ['internal_field' => 'status', 'external_field' => 'status', 'data_type' => 'attribute', 'parent_id' => 4],
            ['internal_field' => 'date', 'external_field' => 'date', 'data_type' => 'attribute', 'parent_id' => 4],
        ];

        foreach (array_merge($defaultRules, $rules) as $ruleData) {
            MappingRule::create($ruleData + ['endpoint_id' => 22]);
        }
    }
}