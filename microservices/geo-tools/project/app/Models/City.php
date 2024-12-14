<?php

namespace App\Models;

use App\Traits\ConstraintsTrait;
use App\Traits\DBUtilitiesTrait;
use App\Traits\ManageCoordinateTrait;
use App\Traits\WithRestUtilsTrait;
use App\Traits\WithValidationTrait;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\QueryException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property int $id
 * @property string $country_code
 * @property string $postal_code
 * @property string $position
 * @property string $region
 * @property string $region_code
 * @property string $province
 * @property string $sigle_province
 * @property string $latitude
 * @property string $longitude
 */
class City extends Model
{
    use HasFactory,
        WithRestUtilsTrait,
        ConstraintsTrait,
        DBUtilitiesTrait,
        WithValidationTrait,
        ManageCoordinateTrait;

    public $timestamps = false;
    protected $table = 'cities';

    protected $fillable = [
        'id',
        'country_code',
        'postal_code',
        'position',
        'region',
        'region_code',
        'province',
        'sigle_prpvince',
        'latitude',
        'longitude'
    ];

    protected $primaryKey = 'id';


    public function Country(): BelongsTo
    {
        return $this->belongsTo(Country::class, "country_code", "country_code");
    }

    /**
     * Summary of get
     * @param array $filters
     *      int 'id',           
     *      array 'country_code', 
     *      array 'postal_code',
     *      array 'position',  
     *      array 'region',    
     *      array 'region_code',
     *      array 'province',    
     *      array 'sigle_province',
     *      array 'latitude',
     *      array 'longitude',
     *      int    resultPerPage > 0 AND <= const LIMITE_RISULTATI_PAGINA=50;
     *      array ordine ["campo" : "ASC" OR "DESC"]
     *@return array
     *  [
     *      'code' => Http code (200..600)
     *      'response' => ['data' => $rows] $rows risultati paginati
     *  ];
     * @Exception 
     * [
     *          'code' => errori di input dati (400), errori database (500)
     *          'response' => ["error" => $errors] relativo messaggio di errore
     *  ];
     */
    public function get(array $filters): array
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0); //300 seconds = 5 minutes
        include_once 'HttpCodeResponse.php';

        # converte il campo ordine in maiuscolo "asc"="ASC", "desc"="DESC"
        # self::initFieldsUpperCase($filters, $fieldToOrder=['ordine']);

        # validi o trasformi
        $constraint = new Assert\Collection([
            // the keys correspond to the keys in the input array
            'country_code' => new Assert\Required(new Assert\All(self::getRules('country_code'))),
            'postal_code' => new Assert\Optional(self::getRulesForCountry($filters["country_code"] ?? null)),
            'position' => new Assert\Optional(new Assert\All(self::getRules('position'))),
            //Martina Franca
            'region' => new Assert\Optional(new Assert\All(self::getRules('region'))),
            'region_code' => new Assert\Optional(new Assert\All(self::getRules('region_code'))),
            'province' => new Assert\Optional(new Assert\All(self::getRules('province'))),
            //Taranto
            'sigle_province' => new Assert\Optional(new Assert\All(self::getRules('sigle_province'))),
            //TA
            'latitude' => new Assert\Optional(self::getRules("latitude")),
            'longitude' => new Assert\Optional(self::getRules("longitude")),
            'ray' => new Assert\Optional(self::getRules("ray")),
            'ordine' => new Assert\Optional(self::getRules('ordine')),
            'resultPerPage' => new Assert\Optional(self::getRules('resultPerPage'))
        ]);

        # WithValidationTrait
        $errors = self::valida($filters, $constraint);

        if (count($errors)) {
            return [
                'code' => self::HTTP_BAD_REQUEST,
                'response' => ["errors" => $errors]
            ];
        }

        try {
            # join con la tabella country $this->table=cities
            $query = self::join("countries", "countries.country_code", "=", "{$this->table}.country_code")->select('*');

            # filtra i dati
            $query = (isset($filters["country_code"])) ?
                $query->whereIn("countries.country_code", $filters["country_code"]) : $query;
            $query = (isset($filters["postal_code"])) ?
                $query->whereIn("postal_code", $filters["postal_code"]) : $query;
            $query = (isset($filters["position"])) ?
                $query->whereIn("position", $filters["position"]) : $query;
            $query = (isset($filters["region"])) ?
                $query->whereIn("region", $filters["region"]) : $query;
            $query = (isset($filters["region_code"])) ?
                $query->whereIn("region_code", $filters["region_code"]) : $query;
            $query = (isset($filters["province"])) ?
                $query->whereIn("province", $filters["province"]) : $query;
            $query = (isset($filters["sigle_province"])) ?
                $query->whereIn("sigle_province", $filters["sigle_province"]) : $query;

            # se esiste il raggio sarà possibile filtrare tutte le localita vicine alla posizione
            if (isset($filters["ray"]) and isset($filters['latitude']) and ($filters['longitude'])) {
                // calcola le posizioni
                // con il raggio avrò una latitudine + raggio ed una latitudine - raggio
                // ed una longitudine + raggio ed una longitudine - raggio
                $posOver = self::add_meters($filters["latitude"], $filters["longitude"], $filters["ray"]);
                $posUnder = self::add_meters($filters["latitude"], $filters["longitude"], $filters["ray"]);

                // prepare una condizione where di filtro al db
                $query = $query->where(
                    ["latitude", ">=", $posUnder["latitude"]],
                    ["latitude", "<=", $posOver["latitude"]],
                    ["longitude", ">=", $posUnder["longitude"]],
                    ["longitude", "<=", $posOver["longitude"]]
                );
            }

            # ordina
            $query = isset($filters['ordine']) ? self::ordina($query, $filters['ordine']) : $query;

            # DBUTilitities::paginate 
            # se $resultPerPage>LIMITE_RISULTATI_PAGINA prende il limite
            $rows = self::paginate(self::paginate($filters["resultPerPage"]));

            return [
                'code' => self::HTTP_OK,
                'response' => ['data' => $rows]
            ];

        } catch (QueryException | Exception $e) {
            rollback();
            return [
                "code" => self::HTTP_INTERNAL_SERVER_ERROR,
                "response" => ["message" => ERRORE_DATABASE]
            ];
        }
    }
}