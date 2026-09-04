<?php

namespace App\Traits;

use Exception;

trait CustomAssertsTrait
{
    /**
     * valida il tipo di dato
     *
     * @param mixed $value valore da valutare
     * @param callable|string $type se $type è una funzione viene eseguita altrimenti viene utilizzato come tipo (int, string, ...) per la validazione di $value
     * @param boolean $nullable se true accetta $value a null
     * @return void
     */
    public function assertType($value, callable|string $type, bool $nullable = true): void
    {
        if ($nullable and is_null($value)) {
            $this->assertNull($value);
            return;
        }

        if (is_callable($type)) {
            call_user_func_array($type, [$value]);
            return;
        }
        switch ($type) {
            case 'int':
                $this->assertIsInt($value);
                break;
            case 'string':
                $this->assertIsString($value);
                break;
            case 'array':
                $this->assertIsArray($value);
                break;
            case 'bool':
                $this->assertIsBool($value);
                break;
            case 'float':
                $this->assertIsFloat($value);
                break;
            default:
                throw new Exception("Type non valido");
        }
    }
}
