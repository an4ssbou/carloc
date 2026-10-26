<?php

namespace Tests\Feature;

use App\Models\Agency;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgencyCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_agency_is_visible_in_public_catalog(): void
    {
        $agency = Agency::factory()->active()->create([
            'name' => 'Agence Catalogue Synthetique',
        ]);
        Vehicle::factory()->create(['agency_id' => $agency->id]);

        $this->get('/agencies')
            ->assertOk()
            ->assertSee('Agence Catalogue Synthetique');
    }
}
