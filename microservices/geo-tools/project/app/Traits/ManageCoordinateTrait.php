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

        $lat1Radians = $posizione1['latitude'] * pi() / 180;
        $lng1Radians = $posizione1['longitude'] * pi() / 180;

        $lng2Radians = $posizione2['longitude'] * pi() / 180;
        $lat2Radians = $posizione2['latitude'] * pi() / 180;

        $x1 = self::RAGGIO_TERRA * cos($lat1Radians) * cos($lng1Radians);
        $y1 = self::RAGGIO_TERRA * cos($lat1Radians) * sin($lng1Radians);
        $z1 = self::RAGGIO_TERRA * sin($lat1Radians);
        $x2 = self::RAGGIO_TERRA * cos($lat2Radians) * cos($lng2Radians);
        $y2 = self::RAGGIO_TERRA * cos($lat2Radians) * sin($lng2Radians);
        $z2 = self::RAGGIO_TERRA * sin($lat2Radians);

        $somme = (($x2 - $x1) ^ 2) + (($y2 - $y1) ^ 2) + (($z2 - $z1) ^ 2);
        return sqrt($somme);
    }
}