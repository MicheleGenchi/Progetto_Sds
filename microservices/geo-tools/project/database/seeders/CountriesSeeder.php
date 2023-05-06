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

        if ($isEmpty) {
            self::loadData();
        }
    }

    /**
     * metodo usato nella migrazione 
     * lettura del file geoNazione.csv 
     * scrittura dati sulla tabella geoNazione       
     * 
     * Summary of loadCSV
     * @return int|string
     *   int  il numero di righe scritte nella tabella geoNazione
     *   string messaggio di errore
     */
    public function loadData(): string
    {
        $dirs = array_diff(scandir(self::PATH), array('.', '..'));

        foreach ($dirs as $file) {
            beginTransaction();
            try {
                $count = 0; //conta le righe scritte nel db
                $rows=[];
                # richiedi $rows qui
                require(self::PATH."/{$file}");
                if (empty($rows)) {
                    throw new Exception("Array vuoto in ${self::PATH}/{$file}");
                }
                    # scrive array $rows sulla tabella countries
                    foreach ($rows as $row) {
                        $country = new Country();
                        $country->country_code = $row["country_code"];
                        $country->country = $row["country"];
                        $country->save();
                        echo '.';
                        $count++;
                    };
                    commit();
                return "Scrittura di " . $count . " righe nella tabella countries";
            } catch (ErrorException $error) {
                return "Scrittura fallita\n" . $error->getMessage();
            } catch (Exception $e) {
                return "Scrittura fallita\n" . $e->getMessage();
            }
        }
        return "";
    }
}