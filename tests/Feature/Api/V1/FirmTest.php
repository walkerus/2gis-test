<?php

namespace Tests\Feature;

use App\Models\Rubric;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FirmTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        DB::statement('TRUNCATE firms, rubrics, buildings RESTART IDENTITY;');
    }

    public function testGetAllFirmsInBuildingNotFound(): void
    {
        $response = $this->get('/api/v1/firms/building/1');

        $response->assertStatus(400);
        $response->assertExactJson(['errors' => [['title' => 'building_not_found', 'status' => 400]]]);
    }

    public function testGetAllFirmsInBuilding(): void
    {
        factory(\App\Models\Building::class)->create([
            'id' => 1
        ]);

        $rubric = new Rubric();
        $rubric->id = 1;
        $rubric->name = 'Foo';
        $rubric->ancestors = '{}';
        $rubric->save();

        factory(\App\Models\Firm::class)->create([
            'id' => 1,
            'building_id' => 1,
            'name' => 'Bar',
            'phones' => '{8-800-5555-35-35, 123}',
            'rubrics' => '{1}',
        ]);

        factory(\App\Models\Firm::class)->create([
            'id' => 2,
            'building_id' => 1,
            'name' => 'Foo',
            'phones' => '{8-800-5555-35-36, 125}',
            'rubrics' => '{1}',
        ]);

        $response = $this->get('/api/v1/firms/building/1');

        $response->assertStatus(200);
        $response->assertExactJson([
            "data" => [
                [
                    "id" => 1,
                    "type" => "firms",
                    'attributes' => [
                        "name" => "Bar",
                        "phones" => [
                            "8-800-5555-35-35",
                            "123",
                        ],
                    ],
                    "relationships" => [
                        'rubrics' => [
                            'data' => [
                                [
                                    "id" => 1,
                                    "type" => "rubrics",
                                ]
                            ]
                        ],
                        'building' => [
                            'data' => [
                                'id' => 1,
                                'type' => 'buildings'
                            ]
                        ]
                    ],
                ],
                [
                    "id" => 2,
                    "type" => "firms",
                    'attributes' => [
                        "name" => "Foo",
                        "phones" => [
                            "8-800-5555-35-36",
                            "125",
                        ],
                    ],
                    "relationships" => [
                        'rubrics' => [
                            'data' => [
                                [
                                    "id" => 1,
                                    "type" => "rubrics",
                                ]
                            ]
                        ],
                        'building' => [
                            'data' => [
                                'id' => 1,
                                'type' => 'buildings'
                            ]
                        ]
                    ],
                ],
            ]
        ]);
    }

    public function testGetAllFirmsInRubricNotFound(): void
    {
        $response = $this->get('/api/v1/firms/rubric/1');

        $response->assertStatus(400);
        $response->assertExactJson(['errors' => [['title' => 'rubric_not_found', 'status' => 400]]]);
    }

    public function testGetAllFirmsInRubric(): void
    {
        $expectedData = [
            [
                'id' => 2,
                'type' => 'firms',
                'attributes' => [
                    'name' => 'foo',
                    'phones' => [
                        '111-1',
                        '222-2',
                    ]
                ],
                'relationships' => [
                    'building' => [
                        'data' => [
                            'id' => 1,
                            'type' => 'buildings'
                        ]
                    ],
                    'rubrics' => [
                        'data' => [
                            [
                                'id' => 2,
                                'type' => 'rubrics'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 3,
                'type' => 'firms',
                'attributes' => [
                    'name' => 'bar',
                    'phones' => [
                        '111-2',
                        '222-3',
                    ]
                ],
                'relationships' => [
                    'building' => [
                        'data' => [
                            'id' => 1,
                            'type' => 'buildings'
                        ]
                    ],
                    'rubrics' => [
                        'data' => [
                            [
                                'id' => 3,
                                'type' => 'rubrics'
                            ]
                        ]
                    ]
                ]
            ],
        ];

        $rubric = new Rubric();
        $rubric->id = 1;
        $rubric->name = 'Foo';
        $rubric->ancestors = '{}';
        $rubric->save();

        $rubric = new Rubric();
        $rubric->id = 2;
        $rubric->name = 'Bar';
        $rubric->ancestors = '{1}';
        $rubric->save();

        $rubric = new Rubric();
        $rubric->id = 3;
        $rubric->name = 'FooBar';
        $rubric->ancestors = '{1,2}';
        $rubric->save();

        factory(\App\Models\Building::class)->create([
            'id' => 1
        ]);

        factory(\App\Models\Firm::class)->create([
            'id' => $expectedData[0]['id'],
            'building_id' => 1,
            'name' => $expectedData[0]['attributes']['name'],
            'phones' => '{111-1, 222-2}',
            'rubrics' => '{2}',
        ]);

        factory(\App\Models\Firm::class)->create([
            'id' => $expectedData[1]['id'],
            'building_id' => 1,
            'name' => $expectedData[1]['attributes']['name'],
            'phones' => '{111-2, 222-3}',
            'rubrics' => '{3}',
        ]);

        factory(\App\Models\Firm::class)->create([
            'id' => 10,
            'building_id' => 1,
            'name' => 'firm name',
            'phones' => '{111-4, 222-5}',
            'rubrics' => '{1}',
        ]);

        $response = $this->get('/api/v1/firms/rubric/2');

        $response->assertStatus(200);
        $response->assertExactJson([
            'links' => [
                'self' => env('APP_URL') . '/api/v1/firms/rubric/2?page=1',
                'last' => env('APP_URL') . '/api/v1/firms/rubric/2?page=1',
            ],
            'data' => $expectedData
        ]);
    }
}
