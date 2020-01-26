<?php

namespace Tests\Feature;

use App\Models\Rubric;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FirmTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllFirmsInBuildingNotFound(): void
    {
        $response = $this->get('/api/v1/firm/building/1');

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
            'phones' => '{8-800-5555-35-35, 123}',
            'rubrics' => '{1}',
        ]);

        factory(\App\Models\Firm::class)->create([
            'id' => 2,
            'building_id' => 1,
            'phones' => '{8-800-5555-35-36, 125}',
            'rubrics' => '{1}',
        ]);

        $response = $this->get('/api/v1/firm/building/1');

        dd($response->getContent());

        $response->assertStatus(200);
    }
}
