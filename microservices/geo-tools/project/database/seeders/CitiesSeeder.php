<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\CsvImport;
use App\Models\Geo;
use ErrorException;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $isEmpty = DB::table('cities')->select('*')->count() <= 0;

        if ($isEmpty) {
            self::loadData();
        }
    }

    
     /**
     * metodo usato nella migrazione 
     * lettura del file geo.csv scaricato dal sito opendatasoft 
     * (https://public.opendatasoft.com/explore/dataset/geonames-postal-code/table/)
     * scrittura dati sulla tabella geoNazione       
     * 
     * Summary of loadCSV
     * @return int|string
     *   int  il numero di righe scritte nella tabella geoNazione
     *   string messaggio di errore
     */
    
     public function loadData(): string
     {
         $dirs = array_diff(scandir("./database/seeders/data/Cities"), array('.', '..'));

         foreach ($dirs as $file) {
            beginTransaction();
            try 
            {
                # require('./database/seeders/data/Cities.php');
                require("./database/seeders/data/Cities/{$file}");
                $count=0;
                # scrive array geoNazione.php su tabella 
                foreach ($rows as $row) {
                    $city = new City();
                    $city->country_code = $row["country_code"];
                    $city->postal_code = $row["postal_code"];
                    $city->position = $row["position"];
                    $city->region = $row["region"];
                    $city->region_code = $row["region_code"];
                    $city->province = $row["province"];
                    $city->sigle_province = $row["sigle_province"];
                    $city->latitude = $row["latitude"];
                    $city->longitude = $row["longitude"];
                    $city->save();
                    echo '.';
                    $count++;
                };
                commit();
                return "Scrittura di ".$count." righe nella tabella cities";
            } catch (ErrorException $error) {
                return "Scrittura fallita\n".$error->getMessage();
            } catch (Exception $e) {
                return "Scrittura fallita\n".$e->getMessage();
            }
        }
        return "";
    }
}
