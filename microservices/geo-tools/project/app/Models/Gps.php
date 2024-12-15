<?php

namespace App\Models;

use App\Traits\ConstraintsTrait;
use App\Traits\WithValidationTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ManageCoordinateTrait;
use App\Traits\WithRestUtilsTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Summary of Gps
 *    regex [latitudine, longitudine] coordinate corrette
 *    regex [latitudine, longitudine] coordinate daVerificare
 */
class Gps extends Model
{

    use HasFactory, ConstraintsTrait, WithValidationTrait, ManageCoordinateTrait, WithRestUtilsTrait;

    /**
     * Verifica la distanza tra due punti geografici
     * Summary of verificaDistanzaTraDueCoordinate
     * @param array $data 
     *      real latitude
     *      real longitude
     *      array verification_data :
     *              real latitude,
     *              real longitude,
     *              int  precision
     * @return array
     *      [
     *          'code': code http,
     *          'response': 'Valida'|'Errata'  
     *      ]
     * #Exception
     *      [
     *          'code': code http,
     *          'response': 'messaggio di eccezione'  
     *      ] 
     */
    public function verifica_posizione(array $data): array
    {
        include_once 'HttpCodeResponse.php';

        $constraints = new Collection([
            'latitude' => new Assert\Required(self::getRules('latitude')),
            'longitude' => new Assert\Required(self::getRules('longitude')),
            'verification_data' => new Assert\Collection([
                // the keys correspond to the keys in the input array
                'latitude' => new Assert\Required(self::getRules('latitude')),
                'longitude' => new Assert\Required(self::getRules('longitude')),
                'precision' => new Assert\required(self::getRules('precision'))
            ])
        ]);


        # WithValidationTrait $data
        $errors = self::valida($data, $constraints);

        if (count($errors)) {
            return [
                'code' => self::HTTP_BAD_REQUEST,
                'response' => ["error" => $errors]
            ];
        }
        
        $distanza = self::formula_distanza(
            [
                "latitude" => $data['latitude'],
                "longitude" => $data['longitude']
            ],
            [
                "latitude" => $data['verification_data']['latitude'],
                "longitude" => $data['verification_data']['longitude']
            ]
        );
        return [
            'code' => self::HTTP_OK,
            'response' =>
                [
                    'data' =>
                        [
                            'differenza_posizioni' => $distanza,
                            'Posizione' => ($distanza < $data['verification_data']['precision']) ? 'Valida' : 'Errata']
                        ]
                ];
    }
}