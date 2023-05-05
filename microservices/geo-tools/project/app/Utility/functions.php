<?php

use Illuminate\Support\Facades\DB;



/**
 * Summary of beginTransaction
 * Descrizione:
 *   mysql non gestisce le transazioni innestate in ambiente di Test
 *   quindi le operazioni di inizio transazione, commit e rollback
 *   non devono essere considerate in ambiente di test       
 * @return void
 */
function beginTransaction()
{
    if (env('TEST', false)) {
        return;
    }

    DB::beginTransaction();
}

/**
 * Summary of commit
 * @return void
 */
function commit()
{
    if (env('TEST', false)) {
        return;
    }

    DB::commit();
}

/**
 * Summary of rollback
 * @return void
 */
function rollback()
{
    if (env('TEST', false)) {
        return;
    }

    DB::rollback();
}