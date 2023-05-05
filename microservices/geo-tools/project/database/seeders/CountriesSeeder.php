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
        $dirs = array_diff(scandir("./database/seeders/data/Countries"), array('.', '..'));

        foreach ($dirs as $file) {
            beginTransaction();
            try 
            {
                # require('./database/seeders/data/Cities.php');
                require("./database/seeders/data/Countries/{$file}");
                $count = 0;
                # scrive array geoNazione.php su tabella 
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