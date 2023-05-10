<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\GeoNazione;
use App\Traits\WithRestUtilsTrait;
use Database\Seeders\CsvImport;
use ErrorException;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    use WithRestUtilsTrait;

    const PATH = "./database/seeders/data/Countries";
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $isEmpty = DB::table('countries')->select('*')->count() <= 0;
            $ris = self::loadData();
            print_r($ris);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * metodo usato nella migrazione 
     * lettura del file geoNazione.csv 
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
            foreach ($dirs as $i => $file) {
                $count = 0; //conta le righe scritte nel db
                $rows = [];
                # richiedi $rows qui
                require(self::PATH . "/{$file}");

                # scrive array $rows sulla tabella countries
                if (empty($rows)) {
                    throw new Exception("\033[31mArray vuoto in " . self::PATH . "/{$file} \033[37m ");
                }

                beginTransaction();
                try {
                    # scrive array $rows sulla tabella countries
                    foreach ($rows as $row) {
                        $country = Country::where("country_code", $row);

                        # vede se esite già una riga con quelle chiavi uguali nel database    
                        # interrompe la scrittura del file
                        if ($country->first()) {
                            throw new Exception("\033[31mNel file {$file} risultano righe duplicate. \033[37m ", 500);
                        }

                        $country = new Country();
                        $country->country_code = $row["country_code"];
                        $country->country = $row["country"];
                        $country->save();
                        echo '.';
                        $count++;
                    }
                    commit();

                } catch (Exception $erow) {
                    rollback();
                    echo "\033[31m\nScrittura fallita\033[37m\n" . $erow->getMessage();
                }

                $totale += $count;
                echo "\nScrittura di " . --$i .
                    " file su " . count($dirs) .
                    " nella tabella countries\n";
            }
            return [
                "code" => self::HTTP_OK,
                "response" => "scrittura totale di {$totale} righe nella tabella cities"
            ];
        } catch (Exception $e) {
            return new Exception($e->getMessage(), $e->getCode());
        }
    }
}