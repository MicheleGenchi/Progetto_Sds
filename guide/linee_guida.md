# Linee guida di programmazione

## Linee guida sul naming
 + Nomi **classi** in **pascal case** contenenti solo il nome dell'entità a cui si riferiscono. es. le classi che si riferiscono alla tabella `controllo_utente` verranno chiamate `ControlloUtente` e saranno nelle apposite cartelle del progetto
 + Nomi dei **metodi** in **camel case**, ad esempio il metodo di elaborazioni delle buste paga verrà chiamato `elaboraBustePaga`. Unico caso particolare i metodi `private` che inizieranno con underscore es. `_elaboraBustePaga`.
 + Nomi delle **variabili/proprietà** in **snake case**, esempio il campo della data di nascita diventerà `data_nascita` se si tratta di proprietà private ricordarsi l'undescore iniziale, es. `_data_nascita`
 + Le **costanti** che siano globali o di una classe/interfaccia sempre **in maiuscolo** con le parole **separate da undescore**, es. `SONO_UNA_COSTANTE`

## Linee guida metodi base
 + Metodi [CRUD](#crud):
   1. [create](#create) => `create`
   1. [read](#read) => `get` e `getList`
   1. [update](#update) => `update`
   1. [delete](#delete) => `delete`
 + [validators](#validators) => `validate`

#### CRUD
Acronimo che sta per Create, Read, Update, Delete

### create
Valida e crea un record di quella entità

### read
I metodi di lettura saranno 2:
 1. `get` => Recupera un elemento dato il suo `id`
 1. `getList` => Recupera un array di elementi filtrati e paginati

### update
Aggiorna un singolo record

### delete
Riceve una lista di id e li cancella / disattiva (in base all'entità)

### Validators
Il metodo riceverà in input il nome del campo e restituirà un validarore (senza preoccuparsi del fatto che il campo sia obbligatorio o meno). In PHP si consiglia l'utilizzo del costrutto match (disponibile da PHP 8)

## Linee guida database
Ogni modifica/aggiuta alla struttura deve essere effettuata da migrazioni del relativo microservizio. I nomi delle tabelle dovranno coincidere con quelli dei modelli eccezion fatta per la struttura che dovrà essere in snake case (es. `nome_tabella`). Analogamente anche i nomi dei campi dovrà essere in snake case.
Le chiavi primarie dovranno chiamarsi `id` se numeriche e `uuid` per quelle alfanumeriche. Ogni record di ogni tabella dovrà avere inoltre la data di creazione e la data di ultimo aggiornamento. Le Foreign key avvranno come nome colonna id o uuid (a seconda del fatto che la chiave sia numerica o alfanumerica) seguita da _ e nome tabella es. `id_utente` o `uuid_documento`