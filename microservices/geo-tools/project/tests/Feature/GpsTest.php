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
 * Summary of GpsTest
 */
class GpsTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use GetWithBodyTrait;
    
    /**
     * Summary of test_verificaDistanzaTraDueCoordinate
     * test per verificare il corretto calcolo della distanza tra due posizioni
     * @return void
     */
    public function test_verificaDistanzaTraDueCoordinate(): void  
    {
        # $nazione crea 'IT','Italia'
        $country=CountryModel::factory()->create();

        $localita=CityModel::factory(App\Models\Geo::class,[
            'country_code' => 'IT',
            'postal_code' => '74123',
            'position' => 'Taranto',
            'region' => 'Puglia',
            'region_code' => '',
            'province' => 'Taranto',
            'sigle_province' => 'TA',
            'latitude' => 40.4644,
            'longitude' => 17.2471
        ])->make();
        $localita->save();

        $doveMiTrovo=CityModel::factory(App\Models\Geo::class,[
            'country_code' => 'IT',
            'postal_code' => '74123',
            'position' => 'Lama',
            'region' => 'Puglia',
            'region_code' => '',
            'province' => 'Taranto',
            'sigle_province' => 'TA',
            'latitude' => 40.4053, 
            'longitude' => 17.2469
        ])->make();
        $doveMiTrovo->save();

        $response = $this->getWithBody('/api/verifica_posizione', [
            "latitude" => 40.4644,
            "longitude" => 17.2471, # latitudine, longitudine
            "verication_data" =>
                [
                    "latitude" => 40.4053,
                    "longitude" => 17.2469,
                    "precision" => 6 # tolleranza in metri del dispositivo
                ], # latitudine, longitudine
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
