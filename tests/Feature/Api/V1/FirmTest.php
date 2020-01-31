<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Firm;
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
        DB::statement('TRUNCATE firm_rubric, firms, rubrics, buildings RESTART IDENTITY;');
    }

    public function testGetAllFirmsInBuildingNotFound(): void
    {
        $response = $this->get('/api/v1/firms/building/1');

        $response->assertStatus(404);
    }

    public function testGetAllFirmsInBuilding(): void
    {
        factory(Building::class)->create([
            'id' => 1
        ]);

        factory(Firm::class)->create([
            'id' => 1,
            'building_id' => 1,
            'name' => 'Bar',
            'phones' => '{8-800-5555-35-35, 123}',
        ]);

        factory(Firm::class)->create([
            'id' => 2,
            'building_id' => 1,
            'name' => 'Foo',
            'phones' => '{8-800-5555-35-36, 125}',
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
                            'data' => []
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
                            'data' => []
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

        $response->assertStatus(404);
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

        factory(Building::class)->create([
            'id' => 1
        ]);

        factory(Firm::class)->create([
            'id' => $expectedData[0]['id'],
            'building_id' => 1,
            'name' => $expectedData[0]['attributes']['name'],
            'phones' => '{111-1, 222-2}',
        ]);

        factory(Firm::class)->create([
            'id' => $expectedData[1]['id'],
            'building_id' => 1,
            'name' => $expectedData[1]['attributes']['name'],
            'phones' => '{111-2, 222-3}',
        ]);

        factory(Firm::class)->create([
            'id' => 10,
            'building_id' => 1,
            'name' => 'firm name',
            'phones' => '{111-4, 222-5}',
        ]);

        DB::table('firm_rubric')->insert([
            [
                'firm_id' => 2,
                'rubric_id' => 2,
            ],
            [
                'firm_id' => 3,
                'rubric_id' => 3,
            ],
            [
                'firm_id' => 10,
                'rubric_id' => 1,
            ],
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

    public function testGetAllFirmsInRadiusBadRequest(): void
    {
        $response = $this->get('/api/v1/firms/radius');

        $response->assertStatus(422);

        $response = $this->get('/api/v1/firms/radius?radius=1000&latitude=55.9&longitude=90');

        $response->assertStatus(200);
    }

    public function testGetAllFirmsInRadius(): void
    {
        $expectedData = [
            [
                'id' => 2,
                'type' => 'firms',
                'attributes' => [
                    'name' => 'bar',
                    'phones' => [
                        '111-1',
                    ]
                ],
                'relationships' => [
                    'building' => [
                        'data' => [
                            'id' => 2,
                            'type' => 'buildings'
                        ]
                    ],
                    'rubrics' => [
                        'data' => [
                            [
                                'id' => 1,
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
                    'name' => 'boo',
                    'phones' => [
                        '111-1',
                    ]
                ],
                'relationships' => [
                    'building' => [
                        'data' => [
                            'id' => 3,
                            'type' => 'buildings'
                        ]
                    ],
                    'rubrics' => [
                        'data' => [
                            [
                                'id' => 1,
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

        factory(Building::class)->create([
            'id' => 1,
            'latitude' => 30.6,
            'longitude' => 100.6
        ]);

        factory(Building::class)->create([
            'id' => 2,
            'latitude' => 30.501,
            'longitude' => 100.49
        ]);

        factory(Building::class)->create([
            'id' => 3,
            'latitude' => 30.508,
            'longitude' => 100.5
        ]);

        factory(Firm::class)->create([
            'id' => 1,
            'building_id' => 1,
            'name' => 'foo',
            'phones' => '{111-1}',
        ]);

        factory(Firm::class)->create([
            'id' => 2,
            'building_id' => 2,
            'name' => 'bar',
            'phones' => '{111-1}',
        ]);

        factory(Firm::class)->create([
            'id' => 3,
            'building_id' => 3,
            'name' => 'boo',
            'phones' => '{111-1}',
        ]);



        DB::table('firm_rubric')->insert([
            [
                'firm_id' => 1,
                'rubric_id' => 1,
            ],
            [
                'firm_id' => 2,
                'rubric_id' => 1,
            ],
            [
                'firm_id' => 3,
                'rubric_id' => 1,
            ],
        ]);

        $response = $this->get('/api/v1/firms/radius?radius=1000&latitude=30.5&longitude=100.5');

        $response->assertStatus(200);
        $response->assertExactJson([
            'links' => [
                'self' => env('APP_URL') . '/api/v1/firms/radius?page=1&radius=1000&latitude=30.5&longitude=100.5',
                'last' => env('APP_URL') . '/api/v1/firms/radius?page=1&radius=1000&latitude=30.5&longitude=100.5',
            ],
            'data' => $expectedData
        ]);

        $expectedData[] = [
            'id' => 1,
            'type' => 'firms',
            'attributes' => [
                'name' => 'foo',
                'phones' => [
                    '111-1',
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
                            'id' => 1,
                            'type' => 'rubrics'
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->get('/api/v1/firms/radius?radius=20000&latitude=30.5&longitude=100.5');

        $response->assertStatus(200);
        $response->assertExactJson([
            'links' => [
                'self' => env('APP_URL') . '/api/v1/firms/radius?page=1&radius=20000&latitude=30.5&longitude=100.5',
                'last' => env('APP_URL') . '/api/v1/firms/radius?page=1&radius=20000&latitude=30.5&longitude=100.5',
            ],
            'data' => $expectedData
        ]);
    }
}
