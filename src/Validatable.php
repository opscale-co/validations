<?php

declare(strict_types=1);

namespace Opscale\Validations;

use Illuminate\Database\Eloquent\Model;
use Opscale\Validations\Exceptions\MissingValidationRulesException;

trait Validatable
{
    /**
     * Alias method for calling the validate method on saving the model.
     */
    public static function validateOnSaving(): void
    {
        static::saving(function (Model $model): void {
            $model->validate();
        });
    }

    /**
     * Alias method for calling the validate method on creating the model.
     */
    public static function validateOnCreating(): void
    {
        static::creating(function (Model $model): void {
            $model->validate();
        });
    }

    private static function hasValidationRulesDefined(): bool
    {
        if (method_exists(static::class, 'validationRules')) {
            return true;
        }

        return property_exists(static::class, 'validationRules');
    }

    /**
     * Calls the validator instance to validate
     * Also runs the beforeValidation and afterValidation methods if exists.
     */
    public function validate(): void
    {
        if (! self::hasValidationRulesDefined()) {
            throw MissingValidationRulesException::for(static::class);
        }

        if (method_exists($this, 'beforeValidation')) {
            $this->beforeValidation();
        }

        (new ModelValidator($this))->validate();

        if (method_exists($this, 'afterValidation')) {
            $this->afterValidation();
        }
    }
}
