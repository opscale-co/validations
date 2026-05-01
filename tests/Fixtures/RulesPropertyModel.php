<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class RulesPropertyModel extends Model
{
    /** @var array<string, mixed> */
    public static array $validationRules = [
        'name' => 'required|max:5',
        'email' => 'required|email',
    ];

    /** @var array<string, string> */
    public static array $validationMessages = [
        'name.required' => 'The full name is mandatory.',
        'name.max' => 'The :attribute must be 5 characters or less.',
    ];

    /** @var array<string, string> */
    public static array $validationAttributes = [
        'name' => 'Full Name',
    ];

    public $timestamps = false;

    protected $table = 'rules_property_models';

    protected $guarded = [];
}
