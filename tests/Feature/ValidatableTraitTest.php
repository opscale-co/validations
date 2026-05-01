<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Opscale\Validations\Exceptions\MissingValidationRulesException;
use Opscale\Validations\Tests\Fixtures\BeforeOnlyModel;
use Opscale\Validations\Tests\Fixtures\CallbackModel;
use Opscale\Validations\Tests\Fixtures\CreatingOnlyModel;
use Opscale\Validations\Tests\Fixtures\TraitWithoutRulesModel;
use Opscale\Validations\Tests\Fixtures\ValidatedModel;

beforeEach(function (): void {
    Schema::create('validated_models', function (Blueprint $table): void {
        $table->increments('id');
        $table->string('name')->nullable();
    });

    Schema::create('creating_only_models', function (Blueprint $table): void {
        $table->increments('id');
        $table->string('name')->nullable();
    });

    Schema::create('callback_models', function (Blueprint $table): void {
        $table->increments('id');
        $table->string('name')->nullable();
    });

    Schema::create('before_only_models', function (Blueprint $table): void {
        $table->increments('id');
        $table->string('name')->nullable();
    });

    Schema::create('trait_without_rules_models', function (Blueprint $table): void {
        $table->increments('id');
        $table->string('name')->nullable();
    });

    CallbackModel::$callOrder = [];
    BeforeOnlyModel::$callOrder = [];
});

afterEach(function (): void {
    Schema::dropIfExists('validated_models');
    Schema::dropIfExists('creating_only_models');
    Schema::dropIfExists('callback_models');
    Schema::dropIfExists('before_only_models');
    Schema::dropIfExists('trait_without_rules_models');
});

it('persists a model whose attributes pass the rules', function (): void {
    $model = new ValidatedModel;
    $model->name = 'Jane';
    $model->save();

    expect($model->exists)->toBeTrue()
        ->and(ValidatedModel::query()->count())->toBe(1);
});

it('blocks saving when the rules fail and never inserts the row', function (): void {
    $model = new ValidatedModel;
    $model->name = 'this-name-is-way-too-long';

    $threw = false;

    try {
        $model->save();
    } catch (ValidationException) {
        $threw = true;
    }

    expect($threw)->toBeTrue()
        ->and(ValidatedModel::query()->count())->toBe(0);
});

it('blocks updating an existing row when the rules fail', function (): void {
    $model = ValidatedModel::query()->create(['name' => 'Jane']);

    $model->name = 'updated-too-long';

    $threw = false;

    try {
        $model->save();
    } catch (ValidationException) {
        $threw = true;
    }

    expect($threw)->toBeTrue()
        ->and($model->fresh()?->name)->toBe('Jane');
});

it('only validates on creating when validateOnCreating is used', function (): void {
    $model = CreatingOnlyModel::query()->create(['name' => 'Jane']);

    $model->name = 'this-update-bypasses-create-rules';
    $model->save();

    expect($model->fresh()?->name)->toBe('this-update-bypasses-create-rules');
});

it('rejects creating when the create-context rule fails', function (): void {
    $threw = false;

    try {
        CreatingOnlyModel::query()->create(['name' => 'this-is-too-long-for-create']);
    } catch (ValidationException) {
        $threw = true;
    }

    expect($threw)->toBeTrue()
        ->and(CreatingOnlyModel::query()->count())->toBe(0);
});

it('runs beforeValidation and afterValidation hooks in the right order', function (): void {
    $model = new CallbackModel;
    $model->save();

    expect(CallbackModel::$callOrder)->toBe(['before', 'after'])
        ->and($model->fresh()?->name)->toBe('auto-filled');
});

it('throws when the trait is used but no validation rules are declared', function (): void {
    $model = new TraitWithoutRulesModel;
    $model->name = 'whatever';

    $model->save();
})->throws(MissingValidationRulesException::class, 'uses the Validatable trait but defines no validation rules');

it('throws on direct validate() call when the trait is used without rules', function (): void {
    $model = new TraitWithoutRulesModel;

    $model->validate();
})->throws(MissingValidationRulesException::class);

it('runs beforeValidation but skips afterValidation when validation fails', function (): void {
    $model = new BeforeOnlyModel;

    $threw = false;

    try {
        $model->save();
    } catch (ValidationException) {
        $threw = true;
    }

    expect($threw)->toBeTrue()
        ->and(BeforeOnlyModel::$callOrder)->toBe(['before']);
});
