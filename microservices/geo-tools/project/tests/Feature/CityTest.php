<?php

namespace Tests\Feature;

use App\Traits\GetWithBodyTrait;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\City as CityModel;
use App\Models\Country as CountryModel;

/**
 * Summary of GeoTest
 */
class GeoTest extends TestCase
{

    use DatabaseMigrations;
    use RefreshDatabase;
    use GetWithBodyTrait;
    
    /**
     * Summary of test_getGeo
     * test di recupero informazioni dalla tabella geo
     * @return void
     */
    public function test_getCity(): void  
    {
        $nazione=CountryModel::factory()->create();
        $city = CityModel::factory()->create();
        $response = $this->getWithBody('/api/cittaFiltrate', [
            'country_code' => ['IT']
        ]);

        $body = json_decode($response->getContent(), true);
        # la risposta deve essere di tipo json
        $response->assertStatus(200)->assertJson([
            'data' => true,
        ]);
        # la chiave data nella risposta deve essere un array
        # e non deve essere vuota
        $this->assertIsArray($body['data']);
        $this->assertNotEmpty($body['data']);
    }
}
