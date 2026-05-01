<?php

declare(strict_types=1);

namespace Opscale\Validations\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

final class RulesMethodModel extends Model
{
    public $timestamps = false;

    protected $table = 'rules_method_models';

    protected $guarded = [];

    /**
     * @return array<string, mixed>
     */
    public function validationRules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationMessages(): array
    {
        return [
            'email.required' => 'Email is needed.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'email' => 'E-mail Address',
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function validationData(array $data): array
    {
        return array_merge($data, [
            'email' => isset($data['email']) ? mb_strtolower((string) $data['email']) : null,
            'computed' => 'injected-by-validation-data',
        ]);
    }
}
