<?php

namespace App\Http\Controllers;

use App\Models\Gps as GpsModel;
use App\Traits\ConstraintsTrait;
use App\Traits\WithRestUtilsTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Summary of GpsUtilitiesController
 */
class GpsController extends BaseController
{
    use ConstraintsTrait, 
        WithRestUtilsTrait;

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
        {
            "latitude": 0.0,
            "longitude": 0.0,
            "verification_data":{
                "precisione": 0.0,
                "latitude": 0.0,
                "longitude": 0.0
            }
        } 
        */
        
        try {
            $ris = GpsModel::verifica_posizione($request->all());
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