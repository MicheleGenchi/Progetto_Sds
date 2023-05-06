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

/**
 * Summary of CitiesSeeder
 */
class CitiesSeeder extends Seeder
{
    Const PATH="./database/seeders/data/Cities";
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
     * @return void
     *   int  il numero di righe scritte nella tabella geoNazione
     *   string messaggio di errore
     */

    public function loadData()
    {
        $dirs = array_diff(scandir(self::PATH), array('.', '..'));
        $totale=0;
        # per ogni file contenuto nella cartella $dirs
        foreach ($dirs as $file) {
            beginTransaction();
            try {
                $count = 0; // conta le righe scritte nel db
                $rows=[];
                # richiedi $rows qui
                require(self::PATH."/{$file}");
                # scrive array $rows sulla tabella cities
                if (empty($rows)) {
                    throw new Exception("Array vuoto in ${self::PATH}/{$file}");
                }    
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
                    commit();
                }
                $totale+=--$count;
                echo "\nScrittura di {$count} righe del file {$file} nella tabella cities";
            } catch (ErrorException $error) {
                echo "\nScrittura fallita\n" . $error->getMessage();
            } catch (Exception $e) {
                echo "\nScrittura fallita\n" . $e->getMessage();
            }
        }
        echo "\nscrittura totale di {$totale} righe nella tabella cities";
    }
}