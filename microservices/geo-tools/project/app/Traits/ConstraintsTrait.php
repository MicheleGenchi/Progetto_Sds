<?php

namespace App\Traits;

use Exception;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;
/**
 * Summary of ConstraintsTrait
 */
trait ConstraintsTrait
{
    use DBUtilitiesTrait;

    /**
     * Summary of getRules
     * @param string $column
     *   $column => un campo della tabella geo_nazione o geo del database mondial
     * @return array
     *  restituisce un array di assert per il campo richiesco $column
     */
    public static function getRules(string $column): array
    {
        $matched= match ($column) {
            'country_code' => [ new Assert\NotBlank(), new Assert\Regex(['pattern' => '/^[A-Z]{2}$/']) ],
            'country' => [ new Assert\NotBlank() ],
            'position' => [ new Assert\NotBlank() ],
            'region' => [ new Assert\NotBlank() ],
            'region_code' => [ new Assert\NotBlank() ],
            'province' => [new Assert\NotBlank() ],
            'sigle_province' => [new Assert\Regex(['pattern' => '/^([A-Z]{2,3})|([\/d]{0,5})|(^$)$/']) ], # 0..99773 O DA AAA..ZZZ
            'latitude' => [new Type(['int','float']), new GreaterThanOrEqual(-90), new LessThanOrEqual(90)], 
            'longitude' => [new Type(['int','float']), new GreaterThanOrEqual(-180), new LessThanOrEqual(180)],
            'precision' => [new Type('int'), new GreaterThanOrEqual(1)],
            'ray' => [new Type('int'), new GreaterThanOrEqual(1)],
            'resultPerPage' => [new Type('int'), 
                                new GreaterThanOrEqual(1),
                                new LessThanOrEqual(self::LIMITE_RISULTATI_PAGINA)],
            'ordine' => [ new Assert\All(new Assert\Choice(['ASC', 'DESC']))
            ],
            # cerchi di validare un campo che in realtà non ha una validazione
            default => throw new Exception("Campo $column inesistente!", 500)
        };

        return $matched;
    }

    /**
     * Summary of getRulesForCountry
     * @param array $countrys
     *   per ciascuna nazione di input, nel file postalCode.json prendo la regex per il codice postale
     * @return array
     *   ritorna le regex possibili per le nazioni inserite $country
     */
    public static function getRulesForCountry(array $countrys=null):array
    {
        if ($countrys==null)
            return  [];

        $currentDir = getcwd();
        # a ciascuna sigla di nazione il relativo regex di codice postale 
        $regex_postalCode=include('postalCode.php');

        $matched=[];
        foreach ($countrys as $country) {
            $matched=array_merge($matched,[$country => 
                                    new Assert\Optional([
                                        # $regex_postalCode math il country corrente da ciclo foreach
                                        # prende il regex del codice postale per il country
                                        new Assert\Regex(['pattern' => $regex_postalCode->$country]) 
                                    ])
                                ]
                    );  
        }   
        $matched_country=[new Assert\Collection($matched)];
        
        return $matched_country;
    }
}
