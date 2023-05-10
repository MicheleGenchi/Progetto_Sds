<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\CsvImport;
use App\Models\Geo;
use App\Traits\WithRestUtilsTrait;
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
    use WithRestUtilsTrait;

    const PATH = "./database/seeders/data/Cities";
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $isEmpty = DB::table('cities')->select('*')->count() <= 0;
            $ris = self::loadData();
            print_r($ris);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * metodo usato nella migrazione 
     * lettura del file geo.csv scaricato dal sito opendatasoft 
     * (https://public.opendatasoft.com/explore/dataset/geonames-postal-code/table/)
     * scrittura dati sulla tabella geoNazione       
     * 
     * Summary of loadCSV
     * @return array|Exception
     *   int  il numero di righe scritte nella tabella geoNazione
     *   string messaggio di errore
     */

    public function loadData(): array|Exception
    {
        try {
            $dirs = array_diff(scandir(self::PATH), array('.', '..'));

            $totale = 0;
            $errorFiles=[];
            # per ogni file contenuto nella cartella $dirs
            foreach ($dirs as $i => $file) {
                $count = 0; // conta le righe scritte nel db
                $rows = [];
                # richiedi $rows qui
                require(self::PATH . "/{$file}");

                # scrive array $rows sulla tabella cities
                if (empty($rows)) {
                    throw new Exception("\033[31mArray vuoto in " . self::PATH . "/{$file} \033[37m ");
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
                            array_push($errorFiles, $file);
                            throw new Exception("\033[31mNel file {$file} risultano righe duplicate. \033[37m ", 500);
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
                    echo "\033[31m\nScrittura fallita\033[37m\n" . $erow->getMessage();
                }

                $totale += $count;
                echo "\033[93m\nScrittura di " . --$i .
                    " file su " . count($dirs) .
                    " nella tabella cities\033[37m\n ";
            }
            return [
                "code" => self::HTTP_OK,
                "response" => "scrittura totale di {$totale} righe nella tabella cities",
                "errors" => $errorFiles
            ];
        } catch (Exception $e) {
            return new Exception($e->getMessage(), $e->getCode());
        }
    }
}