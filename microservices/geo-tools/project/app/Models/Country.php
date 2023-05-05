<?php

namespace App\Models;

use App\Traits\ConstraintsTrait;
use App\Traits\DBUtilitiesTrait;
use App\Traits\WithRestUtilsTrait;
use App\Traits\WithValidationTrait;
use Exception;
use App\Traits\HttpCodeResponseTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @property string $country_code
 * @property string $country
 */
class Country extends Model
{
    use HasFactory, 
        WithRestUtilsTrait, 
        ConstraintsTrait, 
        DBUtilitiesTrait, 
        WithValidationTrait;
        
    /**
     * Summary of timestamps
     * @var bool
     */
    public $timestamps = false;
    /**
     * Summary of table
     * @var string
     */
    protected $table = 'countries';

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'country_code',
        'country'
    ];

    /**
     * Summary of primaryKey
     * @var string
     */
    protected $primaryKey = 'country_code';
    /**
     * Summary of incrementing
     * @var
     */
    public $incrementing = false;
    /**
     * Summary of keyType
     * @var string
     */
    protected $keyType = 'string';


    /**
     * Summary of geos
     * join with table geo (one to many)
     * @return HasMany
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_code', 'country_code');
    }

    
    /**
     * Summary of get
     * @param array $filters
     *      array country_code
     *      array country
     *      int    resultPerPage > 0 AND <= const LIMITE_RISULTATI_PAGINA=50;
     *      array ordine ["campo" : "ASC" OR "DESC"]
     *@return array
     *  [
     *      'code' => 200
     *      'response' => array risultati paginati
     *  ];
     * @Exception 
     * [
     *      'code' => errori di input dati (400), errori database (500)
     *      'response' => ["error" => $errors] messaggio di errore
     *  ];
     */
    public function get(array $filters): array
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0); //300 seconds = 5 minutes
        include_once 'HttpCodeResponse.php';

        # converte il campo ordine in maiuscolo "asc"="ASC", "desc"="DESC"
        # self::initFieldsUpperCase($filters, $fieldUpper=['ordine']);

        # validi o trasformi
        $constraint = new Assert\Collection([
            // the keys correspond to the keys in the input array
            'country_code' => new Assert\Optional(new Assert\All(self::getRules('country_code'))),
            'country' => new Assert\Optional(new Assert\All(self::getRules('country'))),
            'ordine' => new Assert\Optional(self::getRules('ordine')),
            'resultPerPage' => new Assert\Optional(self::getRules('resultPerPage'))
        ]);

        # WithValidationTrait
        $errors = self::valida($filters, $constraint);

        if (count($errors)) {
            return [
                'code' => HTTP_BAD_REQUEST,
                'response' => ["error" => $errors]
            ];
        }

        # join con la tabella geo
        # $query = self::join('geo', 'geo.nazione_code', '=', "{$this->table}.nazione_code")->select('*');
        # chiedere a Cosimo se è necessaria la join

        $query=self::select('*');
        # filtra i dati
        $query = (isset($filters["country_code"])) ? 
                    $query->whereIn("{$this->table}.country_code", $filters["country_code"]) : $query;
        $query = (isset($filters["country"])) ? 
                    $query->whereIn("{$this->table}.country", $filters["country"]) : $query;

        # ordina
        $query = isset($filters['ordine']) ? self::ordina($query, $filters['ordine']) : $query;

        # DBUTilitities::paginate 
        # se $resultPerPage>LIMITE_RISULTATI_PAGINA prende il limite
        $rows=$query->paginate(self::paginate($filters["resultPerPage"] ?? self::LIMITE_RISULTATI_PAGINA));
 
        return [
            'code' => HTTP_OK,
            'response' => ['data' => $rows]
        ];
    }

}