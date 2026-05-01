<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Opscale\Validations\Validatable;

final class ValidatedModel extends Model
{
    use Validatable;

    /** @var array<string, mixed> */
    public static array $validationRules = [
        'name' => 'required|max:5',
    ];

    public $timestamps = false;

    protected $table = 'validated_models';

    protected $guarded = [];

    protected static function booted(): void
    {
        self::validateOnSaving();
    }
}
