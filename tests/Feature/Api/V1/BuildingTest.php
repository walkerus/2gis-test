<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Firm;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BuildingTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        DB::statement('TRUNCATE firm_rubric, firms, rubrics, buildings RESTART IDENTITY;');
    }

    public function testBuildingsEmpty(): void
    {
        $response = $this->get('/api/v1/buildings');

        $response->assertStatus(200);
        $response->assertExactJson([
            'links' => [
                'last' => env('APP_URL') . '/api/v1/buildings?page=1',
                'self' => env('APP_URL') . '/api/v1/buildings?page=1',
            ],
        ]);
    }

    public function testBuildings(): void
    {
        $expectedData = [
            [
                'id' => 2,
                'type' => 'buildings',
                'attributes' => [
                    'address' => 'foo 1',
                    'latitude' => 100.1,
                    'longitude' => 120.2,
                ],
                'relationships' => [
                    'firms' => [
                        'data' => [
                            [
                                'id' => 1,
                                'type' => 'firms'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 3,
                'type' => 'buildings',
                'attributes' => [
                    'address' => 'foo 3',
                    'latitude' => 101.1,
                    'longitude' => 121.2,
                ],
                'relationships' => [
                    'firms' => [
                        'data' => []
                    ]
                ]
            ]
        ];

        factory(Building::class)->create([
            'id' => $expectedData[0]['id'],
            'address' =>  $expectedData[0]['attributes']['address'],
            'latitude' => $expectedData[0]['attributes']['latitude'],
            'longitude' => $expectedData[0]['attributes']['longitude'],
        ]);

        factory(Building::class)->create([
            'id' => $expectedData[1]['id'],
            'address' =>  $expectedData[1]['attributes']['address'],
            'latitude' => $expectedData[1]['attributes']['latitude'],
            'longitude' => $expectedData[1]['attributes']['longitude'],
        ]);

        factory(Firm::class)->create([
            'id' => 1,
            'building_id' => 2,
        ]);

        $response = $this->get('/api/v1/buildings');

        $response->assertStatus(200);
        $response->assertExactJson([
            'links' => [
                'self' => env('APP_URL') . '/api/v1/buildings?page=1',
                'last' => env('APP_URL') . '/api/v1/buildings?page=1',
            ],
            'data' => $expectedData
        ]);
    }
}
