<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MappingRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappingRules = [
            [
                'id' => 1,
                'internal_field' => 'reference',
                'external_field' => 'claimReference',
                'data_type' => 'attribute',
                'parent_id' => null,
                'endpoint_id' => 22,
            ],
            [
                'id' => 2,
                'internal_field' => null,
                'external_field' => 'payer',
                'data_type' => 'object',
                'parent_id' => null,
                'endpoint_id' => 22,
            ],
            [
                'id' => 3,
                'internal_field' => 'payer.name',
                'external_field' => 'payerName',
                'data_type' => 'attribute',
                'parent_id' => 2,
                'endpoint_id' => 22,
            ],
            [
                'id' => 4,
                'internal_field' => 'payer.phone',
                'external_field' => 'payerPhone',
                'data_type' => 'attribute',
                'parent_id' => 2,
                'endpoint_id' => 22,
            ],
            [
                'id' => 5,
                'internal_field' => null,
                'external_field' => 'notes',
                'data_type' => 'array',
                'parent_id' => null,
                'endpoint_id' => 22,
            ],
            [
                'id' => 6,
                'internal_field' => 'authorization_notes',
                'external_field' => 'authorizationNotes',
                'data_type' => 'attribute',
                'parent_id' => 5,
                'endpoint_id' => 22,
            ],
            [
                'id' => 7,
                'internal_field' => 'internal_notes',
                'external_field' => 'internalNotes',
                'data_type' => 'attribute',
                'parent_id' => 5,
                'endpoint_id' => 22,
            ],
            [
                'id' => 8,
                'internal_field' => 'claim_statuses',
                'external_field' => 'claimStatuses',
                'data_type' => 'object_list',
                'parent_id' => null,
                'endpoint_id' => 22,
            ],
            [
                'id' => 9,
                'internal_field' => 'date',
                'external_field' => 'date',
                'data_type' => 'attribute',
                'parent_id' => 8,
                'endpoint_id' => 22,
            ],
            [
                'id' => 10,
                'internal_field' => 'status',
                'external_field' => 'status',
                'data_type' => 'attribute',
                'parent_id' => 8,
                'endpoint_id' => 22,
            ],
        ];

        DB::table('mapping_rules')->insert($mappingRules);
    }
}
