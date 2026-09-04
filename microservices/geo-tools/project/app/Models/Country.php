<?php

namespace App\Models;

use App\Traits\ConstraintsTrait;
use App\Traits\DBUtilitiesTrait;
use App\Traits\WithRestUtilsTrait;
use App\Traits\WithValidationTrait;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
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
        include_once 'HttpCodeResponse.php';

        # converte il campo ordine in maiuscolo "asc"="ASC", "desc"="DESC"
        # self::initFieldsUpperCase($filters, $fieldUpper=['ordine']);

        # validi o trasformi
        $constraints = new Assert\Collection([
            // the keys correspond to the keys in the input array
            'country_code' => new Assert\Optional(new Assert\All(self::getRules('country_code'))),
            'country' => new Assert\Optional(new Assert\All(self::getRules('country'))),
            'resultPerPage' => new Assert\Optional(self::getRules('resultPerPage')),
            'ordine' => new Assert\Optional(self::getRules('ordine')),
            'page' => new Assert\Optional(self::getRules('page'))
        ]);

        # WithValidationTrait
        $errors = self::valida($filters, $constraints);

        /*
          Essendo dei parametri della richiesta necessari per avere una corretta risposta dal server
          Imposto un valore di default se mancano nella richiesta
          default valore per page è 1 se non esiste nella richiesta
          default valore per risultPerPage é 25 se non esiste nella richiesta 
        */
        $filters["page"] ??= 1;
        $filters["resultPerPage"] ??= 25;

        if (count($errors)) {
            return [
                'code' => self::HTTP_BAD_REQUEST,
                'response' => ["errors" => $errors]
            ];
        }

        # join con la tabella countries (qui non serve la join) $this->table=country
        # $query = self::join('cities', 'cities.country_code', '=', "{$this->table}.country_code")->select('*');
        try 
        {
            $query=self::select('*');
            # filtra i dati
            $query = (isset($filters["country_code"])) ? 
                        $query->whereIn("{$this->table}.country_code", $filters["country_code"]) : $query;
            $query = (isset($filters["country"])) ? 
                        $query->whereIn("{$this->table}.country", $filters["country"]) : $query;

            # ordina
            $query = isset($filters['ordine']) ? self::ordina($query, $filters['ordine']) : $query;

            # DBUTilitities::paginate 
            # Necessita di due parametri: 
            # ResultPerPage => il numero di recordi per pagina
            # Page =>  il numero della pagina che vogliamo visualizzare
            
            #Suddivide i record in pagine prendendo la pagina richiesta $filters['page']
            #e se la pagina richiesta non esiste lancia un'eccezione 
            $paginated = $query->paginate($filters["resultPerPage"], ['*'], 'page', $filters['page']);
            if ($paginated->lastPage()<$filters['page']) {
                throw new Exception("Pagina richiesta {$filters['page']} maggiore dell'ultima pagina {$paginated->lastPage()}", 400);
            }
            return [
                'code' => self::HTTP_OK,
                'response' => ['data' => $paginated],
            ];
        } catch (QueryException | Exception $e)  {
          rollback();
          return [
                "code" => self::HTTP_INTERNAL_SERVER_ERROR,
                "response" => ["message" => $e->getMessage() ?? 'ERRORE_DATABASE']
            ];  
        }
    }

}