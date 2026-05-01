<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class ContextRulesModel extends Model
{
    /** @var array<string, mixed> */
    public static array $validationRules = [
        'name' => [
            'create' => 'required|max:5',
            'update' => 'sometimes|max:5',
        ],
        'email' => [
            'create' => 'required|email',
        ],
        'token' => [
            'update' => 'required|min:10',
        ],
        'always' => 'required|max:3',
    ];

    public $timestamps = false;

    protected $table = 'context_rules_models';

    protected $guarded = [];
}
