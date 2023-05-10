<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\GeoNazione;
use Database\Seeders\CsvImport;
use ErrorException;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{

    Const PATH="./database/seeders/data/Countries";
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $isEmpty = DB::table('countries')->select('*')->count() <= 0;

            self::loadData();
    }

    /**
     * metodo usato nella migrazione 
     * lettura del file geoNazione.csv 
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
        foreach ($dirs as $i => $file) {
            try {
                $count = 0; //conta le righe scritte nel db
                $rows=[];
                # richiedi $rows qui
                require(self::PATH."/{$file}");
                if (empty($rows)) {
                    throw new Exception("Array vuoto in ".${self::PATH}."/{$file}");
                }

                beginTransaction();
                # scrive array $rows sulla tabella countries
                foreach ($rows as $row) {
                    $country = Country::where("country_code", $row);
                        
                    # vede se esite già una riga con quelle chiavi uguali nel database    
                    # interrompe la scrittura del file
                    if ($country->first()) {
                        throw new Exception("<span style=\"color:#AFA;text-align:center;\"> 
                        Nel file {$file} risultano righe duplicate <\span>", 500);
                    }

                    $country= new Country();
                    $country->country_code = $row["country_code"];
                    $country->country = $row["country"];
                    $country->save();
                    echo '.';
                    $count++;

                };


                commit();
                $totale+=$count;
                echo "\nScrittura di ".--$i." file su "
                    .count($dirs)." nella tabella countries\n";

            } catch (ErrorException $error) {
                echo "\nScrittura fallita\n" . $error->getMessage();
            } catch (Exception $e) {
                echo "\nScrittura fallita\n" . $e->getMessage();
            }
        }
        echo "\nscrittura totale di {$totale} righe nella tabella countries";
    }
}