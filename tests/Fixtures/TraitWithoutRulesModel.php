<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Opscale\Validations\Validatable;

final class TraitWithoutRulesModel extends Model
{
    use Validatable;

    public $timestamps = false;

    protected $table = 'trait_without_rules_models';

    protected $guarded = [];

    protected static function booted(): void
    {
        self::validateOnSaving();
    }
}
