<?php

namespace App\Http\Controllers;

use App\Models\Gps;
use App\Traits\WithRestUtilsTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

/**
 * Summary of GpsUtilitiesController
 */
class GpsController extends BaseController
{
    use WithRestUtilsTrait;

    /**
     * Summary of verificaDistanzaTraDueCoordinate
     * @param Request $request
     *      regex [latitudine, logintudine] coordinate corrette => 
     *                                              luogo in cui la timbratura è valida
     *      regex [latitudine, logintudine] coordinate da verificare => 
     *                                              luogo in cui si è verificata la timbratura (dove mi trovo)
     * @return JsonResponse
     * {
     *      "data": [
     *          {
     *              "Posizione": "Valida" o "Errata"
     *          }
     *      ]
     *  }
     */
    public function verifica_posizione(Request $request):JsonResponse
    {
        /* 
            Se nei parametri della richiesta non ho la latitudine e la lognitudine
            ma il postalcode e la posizione posso ricavare la latitudine e la longitudine 
            con una chiamata al controller country con i parametri richiesti
        */
        if (isset($request['position']) && isset($request['postal_code'])) {
            unset($request['latitude']);
            unset($request['longitude']);
            $ctrl = new \App\Http\Controllers\CityController();
            //filtra request con i parametri necessari
            $requestFiltered = $request->except(['verification_data','precision']);

            $cityResponse = $ctrl->get(new Request($requestFiltered));

            $cityData = $cityResponse instanceof JsonResponse ? $cityResponse->getData(true) : $cityResponse;

            //echo var_dump($cityData['data']['data'][0]['latitude']);
            $request->request->add(['latitude' => (float) $cityData['data']['data'][0]['latitude']]);
            $request->request->add(['longitude' => (float) $cityData['data']['data'][0]['longitude']]);
            //return response()->json($request->all(), 200);
        }

        unset($request['country_code']);
        unset($request['position']);
        unset($request['postal_code']);
        
        try {
            $model = new Gps();
            $ris = $model->verifica_posizione($request->all());
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