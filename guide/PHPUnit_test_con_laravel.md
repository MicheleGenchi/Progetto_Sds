# Test di unità o funzionalità con laravel
## Introduzione
I test di un software consentono di effettuare modifiche anche molto complesse del codice in tutta tranquillità poichè dopo aver implementato le suddette modifiche è possibile tramite un semplice comado effettuare i test di ogni caso d'uso.

### Differenza tra test di unità e test di funzionalità
I test si differenziao in **test di unità** (test di piccole porzioni di codice che non interagiscono con una base dati o con altri pezzi di codice) e **test di funzionalità** il cui scopo è testare delle porzioni di codice per valutare, ad esempio, che un endpoint rispoda come ci si aspetta.
Come avrete già intuito noi tratteremo soprattutto test di **funzionalità**, questo per evitare che delle modifiche in corso d'opera possano "rompere" il software, esempio:
Immaginate di effettuare delle modifiche ad un metodo di un *controller* o di un *model* che portano il microservizio a rispondere anzichè con un json di questo tipo:
```JSON
"Valore non consentito"
```
con un json in quest'altro formato
```JSON
{
    "response": "Valore non consentito"
}
```

Come potete immaginare in etrambi i casi il microservizio rispode "*correttamente*" e l'elaborazione ha lo stesso risultato, tuttavia la struttura del body della risposta è variato e questo genererà (nel migliore dei casi) dei bug dell'interfaccia o (nel peggiore dei casi) un blocco completo del front-end o dei software che usano le nostre API.

#### Come impedirlo?
Per impedire questi malfunzionamenti dovuti a errori (spesso accidentali) nella variazione delle risposte del codice, si possono utilizzare i test. I test quindi, serviranno a valurare la struttura delle risposte dei metodi dei vostri controller/model in base agli input ricevuti

## Configurazione dei test
Su questo progetto ho aggiunto un ulteriore database separato per evitare errori/problemi con il database di produzione (sarebbe stato possibile anche aggiungere un ulteriore db sull'istanza mysql del database di produzione). I dati di connessione a questo DB li trovate in [.env-db](microservices/db/.env.db-test).

 1. Nel vostro file `database.php`, che trovate in `microservices/{VOSTRO_MICROSERVIZIO}/project/config/`, dovrete aggiungere le seguenti chiavi associative all'array `connections`
    ```PHP
        'db-test' => [
            'driver'    => 'mysql',
            'host'      => env('TEST_DB_HOST', 'db-test'),
            'database'  => env('TEST_DB_DATABASE', 'test'),
            'username'  => env('TEST_DB_USERNAME', 'test'),
            'password'  => env('TEST_DB_PASSWORD', 'S3rv3r-TEST'),
            'port'      => env('DB_PORT','33060'),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
    ```
 1. Nel file `phpunit.xml` in `microservices/{VOSTRO_MICROSERVIZIO}/project/` dovrete poi aggiungere sotto la chiave `php` le seguenti voci:
    ```XML
    <env name="DB_CONNECTION" value="db-test"/>
    <env name="DB_DATABASE" value="test"/>
    ```
 1. Creare un file `.env.testing` nella directory principale del vostro progetto (la stessa del file `.env`) facedo attenzione a sovrascrivere le seguenti chiavi. In questo modo nel mometo in cui verranno eseguiti i test, laravel prenderà in considerazione il file di .env opportuno
    ```ENV
    DB_CONNECTION=db-test
    DB_HOST=db-test
    DB_PORT=3306
    DB_DATABASE=test
    DB_USERNAME=test
    DB_PASSWORD=S3rv3r-TEST
    ```
 1. Creare il vostro primo test di funzionalità con il comando
    ```BASH
    php artisan make:test NomeClasseTest
    ```