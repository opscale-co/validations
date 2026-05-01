<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Opscale\Validations\Validatable;

final class CallbackModel extends Model
{
    use Validatable;

    /** @var array<string, mixed> */
    public static array $validationRules = [
        'name' => 'required',
    ];

    /** @var array<int, string> */
    public static array $callOrder = [];

    public $timestamps = false;

    protected $table = 'callback_models';

    protected $guarded = [];

    protected static function booted(): void
    {
        self::validateOnSaving();
    }

    public function beforeValidation(): void
    {
        self::$callOrder[] = 'before';
        $this->name = 'auto-filled';
    }

    public function afterValidation(): void
    {
        self::$callOrder[] = 'after';
    }
}
