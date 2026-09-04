<?php 

namespace App\Traits;


/**
 * Summary of ManageCoordinateTrait
 */
trait ManageCoordinateTrait
{


    const RAGGIO_TERRA = 6376.5 * 1000; //raggio della terra in metri

     /**
      * Summary of add_meters
      * @param float $latitude
      * @param float $longitude
      * @param int $meters
      * @return array
      *     ["latitudine" => latitudine + ray]  
      *     ["longitudine" => longitudine + ray]
      */
     protected static function add_meters(float $latitude, float $longitude, int $meters):array 
     {
        $coef = $meters * 0.0000089;
        $new_lat = $latitude + $coef;
        $new_long = $longitude + $coef / cos($latitude * 0.018);
        return [
            "latitude" => $new_lat,
            "longitude" => $new_long    
        ];
     }

     /**
      * Summary of add_meters
      * @param float $latitude
      * @param float $longitude
      * @param int $meters
      * @return array
      *     ["latitudine" => latitudine - ray]  
      *     ["longitudine" => longitudine - ray]
      */
     protected static function min_meters(float $latitude, $longitude, int $meters):array 
     {
        $coef = $meters * 0.0000089;
        $new_lat = $latitude - $coef;
        $new_long = $longitude - $coef / cos($latitude * 0.018);
        return [
            "latitude" => $new_lat,
            "longitude" => $new_long    
        ];
     }

    /**
     * Summary of formula_distanza
     * @param array $posizione1 ["latitude","longitude"]
     * @param array $posizione2 ["latitude","longitude"] 
     * @return float
     *  float :  distanza tra le due posizioni
     */
    protected static function formula_distanza(array $posizione1, array $posizione2):float
    {

        #Recupero valori dai parametri per la formula
        $lat1=$posizione1['latitude'];
        $lat2=$posizione2['latitude'];
        $lon1=$posizione1['longitude'];
        $lon2=$posizione2['longitude'];

        #formula di Haversine.
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
    
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
         
        $c = 2 * asin(sqrt($a));
    
        return self::RAGGIO_TERRA * $c; // Distanza in km

    }
}