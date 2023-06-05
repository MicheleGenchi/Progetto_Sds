<?php

namespace Database\Seeders;

use App\Models\City;
use ErrorException;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Summary of CitiesSeeder
 */
class CitiesSeeder extends Seeder
{
    const PATH = "./database/seeders/data/Cities";
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $isEmpty = DB::table('cities')->select('*')->count() <= 0;

        #if ($isEmpty) {
        self::loadData();
        #}
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
        $totale = 0;
        # per ogni file contenuto nella cartella $dirs
        try {
            foreach ($dirs as $i => $file) {
                $count = 0; // conta le righe scritte nel db
                $rows = [];
                # richiedi $rows qui
                require(self::PATH . "/{$file}");
                # scrive array $rows sulla tabella cities
                if (empty($rows)) {
                    throw new Exception("Array vuoto in " . ${self::PATH} . "/{$file}");
                }

                beginTransaction();
                try {
                    foreach ($rows as $row) {
                        $city = City::
                            where(function ($fn) use ($row) {
                                return $fn->where("country_code", $row["country_code"])
                                    ->where("region_code", $row["region_code"])
                                    ->where("position", $row["position"])
                                    ->where("postal_code", $row["postal_code"]);
                            });

                        # vede se esite già una riga con quelle chiavi uguali nel database    
                        # interrompe la scrittura del file
                        if ($city->first()) {
                            # <span style="color:#AFA;text-align:center;"> #
                            throw new Exception("\033[01;31m Nel file {$file} risultano righe duplicate \033[01;31m", 500);
                        }

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
                    }
                    commit();

                } catch (Exception $erow) {
                    rollback();    
                    echo "\nScrittura fallita\n" . $erow->getMessage();
                }
                
                $totale += $count;
                $nfile=str_replace([".php","temp_"], "", $file, $count);
                echo "\nScrittura di " . $nfile.
                    " file su " . count($dirs).
                    " nella tabella cities\n";
            }
        } catch (ErrorException $error) {
            echo "\nScrittura fallita\n" . $error->getMessage();
        } catch (Exception $e) {
            echo "\nScrittura fallita\n" . $e->getMessage();
        }
        echo "\nscrittura totale di {$totale} righe nella tabella cities";
    }
}