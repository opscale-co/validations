<?php

declare(strict_types=1);

namespace Opscale\Validations\Exceptions;

use RuntimeException;

final class MissingValidationRulesException extends RuntimeException
{
    public static function for(string $modelClass): self
    {
        return new self(sprintf(
            'Model [%s] uses the Validatable trait but defines no validation rules. '
                .'Declare a public static $validationRules property or a validationRules() method.',
            $modelClass
        ));
    }
}
