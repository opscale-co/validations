<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Opscale\Validations\Validatable;

final class CreatingOnlyModel extends Model
{
    use Validatable;

    /** @var array<string, mixed> */
    public static array $validationRules = [
        'name' => [
            'create' => 'required|max:5',
        ],
    ];

    public $timestamps = false;

    protected $table = 'creating_only_models';

    protected $guarded = [];

    protected static function booted(): void
    {
        self::validateOnCreating();
    }
}
