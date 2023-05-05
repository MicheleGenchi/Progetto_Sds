<?php

namespace Tests\Feature;

use App\Traits\GetWithBodyTrait;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Country as CountryModel;

/**
 * Summary of GeoNazioneTest
 */
class CountryTest extends TestCase
{

    use DatabaseMigrations;
    use RefreshDatabase;
    use GetWithBodyTrait;

    /**
     * Summary of test_getNazione
     * test di recupero informazioni sulle nazioni
     * @return void
     */
    public function test_getCountry(): void  
    {
        $nazione = CountryModel::factory()->create();
        $response = $this->getWithBody('/api/nazioniFiltrate', [
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
