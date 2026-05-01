<?php

declare(strict_types=1);

use Illuminate\Validation\ValidationException;
use Opscale\Validations\ModelValidator;
use Opscale\Validations\Tests\Fixtures\BareModel;
use Opscale\Validations\Tests\Fixtures\ContextRulesModel;
use Opscale\Validations\Tests\Fixtures\RulesMethodModel;
use Opscale\Validations\Tests\Fixtures\RulesPropertyModel;

it('returns silently when the model has no rules', function (): void {
    $model = new BareModel;
    $model->setRawAttributes(['name' => 'whatever']);

    $validator = new ModelValidator($model);

    expect($validator->validate())->toBe($validator);
});

it('reads rules, messages and attributes from a static property', function (): void {
    $model = new RulesPropertyModel;
    $model->setRawAttributes([
        'name' => 'Jane',
        'email' => 'jane@example.com',
    ]);

    $validator = new ModelValidator($model);

    expect($validator->validate())->toBe($validator);
});

it('reads rules, messages and attributes from instance methods', function (): void {
    $model = new RulesMethodModel;
    $model->setRawAttributes(['email' => 'JOHN@EXAMPLE.COM']);

    $validator = new ModelValidator($model);

    expect($validator->validate())->toBe($validator);
});

it('throws ValidationException when a rule fails', function (): void {
    $model = new RulesPropertyModel;
    $model->setRawAttributes([
        'name' => 'this-is-too-long',
        'email' => 'jane@example.com',
    ]);

    (new ModelValidator($model))->validate();
})->throws(ValidationException::class);

it('uses custom messages from the static property when validation fails', function (): void {
    $model = new RulesPropertyModel;
    $model->setRawAttributes(['name' => null, 'email' => 'jane@example.com']);

    try {
        (new ModelValidator($model))->validate();
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('name')
            ->and($e->errors()['name'])->toContain('The full name is mandatory.');

        return;
    }

    throw new RuntimeException('Expected ValidationException was not thrown.');
});

it('uses custom attribute names from the static property when validation fails', function (): void {
    $model = new RulesPropertyModel;
    $model->setRawAttributes(['name' => 'too-long', 'email' => 'jane@example.com']);

    try {
        (new ModelValidator($model))->validate();
    } catch (ValidationException $e) {
        expect($e->errors()['name'][0])->toContain('Full Name');

        return;
    }

    throw new RuntimeException('Expected ValidationException was not thrown.');
});

it('resolves create-context rules when the model does not yet exist', function (): void {
    $model = new ContextRulesModel;
    $model->exists = false;
    $model->setRawAttributes([
        'name' => 'Jane',
        'email' => 'jane@example.com',
        'always' => 'abc',
    ]);

    $validator = new ModelValidator($model);

    expect($validator->validate())->toBe($validator);
});

it('fails create-context validation when a create-only field is missing', function (): void {
    $model = new ContextRulesModel;
    $model->exists = false;
    $model->setRawAttributes([
        'always' => 'abc',
    ]);

    try {
        (new ModelValidator($model))->validate();
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKeys(['name', 'email']);

        return;
    }

    throw new RuntimeException('Expected ValidationException was not thrown.');
});

it('skips fields whose context does not match — update ignores create-only fields', function (): void {
    $model = new ContextRulesModel;
    $model->exists = true;
    $model->setRawAttributes([
        'token' => 'longenough',
        'always' => 'abc',
    ]);

    $validator = new ModelValidator($model);

    expect($validator->validate())->toBe($validator);
});

it('enforces update-only rules when the model already exists', function (): void {
    $model = new ContextRulesModel;
    $model->exists = true;
    $model->setRawAttributes([
        'always' => 'abc',
    ]);

    try {
        (new ModelValidator($model))->validate();
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('token');

        return;
    }

    throw new RuntimeException('Expected ValidationException was not thrown.');
});

it('still applies non-context rules across both contexts', function (): void {
    $model = new ContextRulesModel;
    $model->exists = false;
    $model->setRawAttributes([
        'name' => 'Jane',
        'email' => 'jane@example.com',
        'always' => 'too-long',
    ]);

    try {
        (new ModelValidator($model))->validate();
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('always');

        return;
    }

    throw new RuntimeException('Expected ValidationException was not thrown.');
});

it('lets validationData() transform the data before validation runs', function (): void {
    $model = new RulesMethodModel;
    $model->setRawAttributes(['email' => 'CASE-SENSITIVE@EXAMPLE.COM']);

    $validator = new ModelValidator($model);

    expect($validator->validate())->toBe($validator);
});

it('exposes the same instance from initialize() to allow chaining', function (): void {
    $model = new BareModel;
    $model->setRawAttributes([]);

    $validator = new ModelValidator($model);

    expect($validator->initialize())->toBe($validator);
});
