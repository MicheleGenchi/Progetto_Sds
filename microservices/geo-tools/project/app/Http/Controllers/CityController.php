<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Geo;
use App\Traits\WithRestUtilsTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

/**
 * Summary of GeoController
 */
class CityController extends BaseController
{

    use WithRestUtilsTrait;

    /**
     * Summary of get
     * @param Request $request
     *  int id   // incremental int
     *  array country_code  // 2 caratteri che indicano la nazione 'IT'
     *  array postal_code // cap che cambia di formato con la nazione
     *  array position  // la localita (Martina, Lama, San Giorgio, Massafra)
     *  array region  // regioni
     *  array region_code   // codice numerico 
     *  array province   // 'Taranto, Lecce, Bari ...'
     *  array sigle_province          //    'TA, BA, ...'
     *  array latitude  //    'latitudine
     *  array longitude //    'longitudine
     * @return JsonResponse
     *  {
     *      "id": 44167,
     *      "nazione_code": "IT",
     *      "codice_postale": "74122",
     *      "posto": "Taranto",
     *      "regione": "Puglia",
     *      "regione_code": "13",
     *      "provincia": "Taranto",
     *      "pr": "TA",
     *      "posizione": ["40.4644, 17.2471"],
     *      "nazione": "Italy" (join con tablella geo_nazione)
     *  }
     */
    public function get(Request $request):JsonResponse
    {
        try {
            $model = new City();
            $ris =$model->get($request->all());
        } catch (Exception $e) {
            $code = (int) $e->getCode();
            $ris = [
                'response' => $e->getMessage(),
                'code' => self::validateErrorCode($code)
            ];
        }
        return response()->json($ris['response'], $ris['code']);

    }
}