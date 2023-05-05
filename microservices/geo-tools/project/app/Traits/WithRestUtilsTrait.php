<?php

namespace App\Traits;

trait WithRestUtilsTrait
{
    public static function validateErrorCode(int|string $code): int
    {
        return ($code >= 400 and $code < 600) ? $code : 500;
    }
}