<?php

namespace App\Traits;

use Symfony\Component\Validator\Validation;

/**
 * Summary of WithValidationTrait
 */
trait WithValidationTrait {

    /**
     * Summary of valida
     * @param mixed $data
     *      Parametri della richiesta -> $filters
     * @param \Symfony\Component\Validator\Constraint|array $rules
     *      valida ogni parametro (anche quelli relativi a sotto array) con i constraint 
     * @return array
     *  referenziando $prew e $next ad $error
     *  $error conterrà tutti gli errori dati 
     *         dalle violazioni di ciascun parametro della richiesta
     */
    # geoNzione -> $errors = self::valida($filters, $constraint);
    # geo -> $errors = self::valida($filters, $constraint);
     public static function valida(mixed $data,\Symfony\Component\Validator\Constraint|array $rules):array{
        $validator = Validation::createValidator();

        $violations = $validator->validate($data, $rules);

        $errors = [];
        if (count($violations)) {
            foreach ($violations as $violation) {
                preg_match_all('/\[([^\]]+)\]/', $violation->getPropertyPath(), $fields);

                $prew = &$errors;
                foreach( $fields[1] as $field ){
                    $next = &$prew[$field];
                    $prew = &$next;
                }
                $next[] = $violation->getMessage();
            }
        }

        return $errors;
    } 
}