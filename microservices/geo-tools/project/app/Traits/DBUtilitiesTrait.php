<?php

namespace App\Traits;

/**
 * Summary of DBUtilitiesTrait
 */
trait DBUtilitiesTrait
{

	const LIMITE_RISULTATI_PAGINA = 50;

	public static function paginate(int $resultPerPage = self::LIMITE_RISULTATI_PAGINA): int
	{
		return $resultPerPage > self::LIMITE_RISULTATI_PAGINA ? self::LIMITE_RISULTATI_PAGINA : $resultPerPage;
	}

	# ordine generico delle query
	# $query contiene l'oggetto laravel che rappresenta i dati della tabella
	# $ordine ["nome_campo" => "ASC||DESC"]
	/**
	 * Summary of ordina
	 * @param mixed $query
	 * @param array $ordine 
	 * 	// ricordare di indicare la tabella se ci sono campi uguali
	 * @throws \Exception
	 * @return 
	 * 		mixed $query (ordinata)
	 */
	public static function ordina($query, array $ordine)
	{
		if (!empty($ordine)) {
			foreach ($ordine as $fields => $order_type) {
				$order_type = strtoupper($order_type);
				if (!in_array($order_type, ['ASC', 'DESC'])) {
					throw new \Exception("formato ordine non corretto", 400);
				}

				$query->orderby($fields, $order_type);
			}
		}
		return $query;
	}


	/**
	 * Summary of initFieldsUpperCase 
	 * trasforma in maiuscolo tutte le chiavi che matchiano tra $filters e $arrayUpper
	 * @param array $filters //tutti i filtri inseriti dall'utente
	 * @param array $arrayUpper //array di chiavi da ordinare
	 * @return void 
	 * 		$filters Passes it by reference
	 */
	public static function initFieldsUpperCase(array &$filters, array $arrayUpper = []): void
	{
		if (!empty($arrayUpper)) {
			foreach ($arrayUpper as $daCercare) {
				$allKeys = array_keys($filters);
				/*
				0:chiave1
				1:chiave2
				*/
				$found = array_search($daCercare, $allKeys); // restituisce indice della chiave  se trovata

				array_walk($filters[$allKeys[$found]], function (&$current) {
					$current = strtoupper($current);
				});
			}
		}
	}
}