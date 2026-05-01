## Support us

At Opscale, we're passionate about contributing to the open-source community by providing solutions that help businesses scale efficiently. If you've found our tools helpful, here are a few ways you can show your support:

⭐ **Star this repository** to help others discover our work and be part of our growing community. Every star makes a difference!

💬 **Share your experience** by leaving a review on [Trustpilot](https://www.trustpilot.com/review/opscale.co) or sharing your thoughts on social media. Your feedback helps us improve and grow!

📧 **Send us feedback** on what we can improve at [feedback@opscale.co](mailto:feedback@opscale.co). We value your input to make our tools even better for everyone.

🙏 **Get involved** by actively contributing to our open-source repositories. Your participation benefits the entire community and helps push the boundaries of what's possible.

💼 **Hire us** if you need custom dashboards, admin panels, internal tools or MVPs tailored to your business. With our expertise, we can help you systematize operations or enhance your existing product. Contact us at hire@opscale.co to discuss your project needs.

Thanks for helping Opscale continue to scale! 🚀



## Description

Model validation for Laravel applications. An easy validator option for your Eloquent models with flexibility for additional code that can be executed before and after validation.

The package exposes two public symbols:

| Symbol | Kind | Purpose |
|--------|------|---------|
| `Opscale\Validations\Validatable` | Trait | Wires validation into Eloquent lifecycle events and runs optional `beforeValidation` / `afterValidation` hooks |
| `Opscale\Validations\ModelValidator` | Class | Resolves rules / messages / attributes / data from a model and runs Laravel's validator |

All classes ship with `declare(strict_types=1);` and full type hints — your consuming code is expected to do the same.

## Installation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/opscale-co/validations.svg?style=flat-square)](https://packagist.org/packages/opscale-co/validations)

You can install the package via composer:

```bash

composer require opscale-co/validations

```

## Usage

Here the User model is mentioned as an example. You can use this in any model you want.

### Basic Setup

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Opscale\Validations\Validatable;

class User extends Model
{
    use Validatable;

    /** @var array<string, mixed> */
    public static array $validationRules = [
        'name' => 'required|max:10',
        'email' => 'required|email',
    ];

    protected static function booted(): void
    {
        // Validate the model on saving (runs on both create and update)
        static::validateOnSaving();
    }
}
```

> **Trait contract:** if a model uses `Validatable` but declares no rules (neither static
> `$validationRules` nor a `validationRules()` method), calling `validate()` throws
> `Opscale\Validations\Exceptions\MissingValidationRulesException`. Using the trait is the
> explicit opt-in to validation — forgetting to declare rules is a configuration error,
> not a silent no-op.

### Defining Rules, Messages, and Attributes

Rules, messages, and attributes can all be defined as **static properties** or **instance methods**. Methods take priority over properties and offer more flexibility when you need dynamic logic.

#### As static properties

```php
/** @var array<string, mixed> */
public static array $validationRules = [
    'name' => 'required|max:10',
    'email' => 'required|email',
];

/** @var array<string, string> */
public static array $validationMessages = [
    'name.required' => 'Name field is required.',
    'email.email' => 'The given email is in invalid format.',
];

/** @var array<string, string> */
public static array $validationAttributes = [
    'name' => 'User Name',
];
```

#### As instance methods

```php
/** @return array<string, mixed> */
public function validationRules(): array
{
    return [
        'name' => 'required|max:10',
        'email' => 'required|email',
    ];
}

/** @return array<string, string> */
public function validationMessages(): array
{
    return [
        'name.required' => 'Name field is required.',
        'email.email' => 'The given email is in invalid format.',
    ];
}

/** @return array<string, string> */
public function validationAttributes(): array
{
    return [
        'name' => 'User Name',
    ];
}
```

### Context-Aware Rules (Create vs Update)

Each field can carry per-context rules. The context is resolved from `$model->exists`:
new models use the `create` rule, persisted models use the `update` rule. A field whose
context array does not include the current context is skipped entirely.

```php
/** @var array<string, mixed> */
public static array $validationRules = [
    'name' => 'required|max:10',
    'email' => [
        'create' => 'required|email|unique:users',
        'update' => 'required|email',
    ],
    'token' => [
        'update' => 'required|min:32', // only enforced on update
    ],
];
```

### Control the Data Being Validated

You can transform the data that gets validated by adding a `validationData` method:

```php
/**
 * @param  array<string, mixed>  $data
 * @return array<string, mixed>
 */
public function validationData(array $data): array
{
    $data['name'] = mb_strtolower((string) $data['name']);

    return $data;
}
```

### Before and After Validation Hooks

`beforeValidation` runs before `Validator::make()`. `afterValidation` runs only when
validation succeeds — if validation throws `Illuminate\Validation\ValidationException`,
`afterValidation` is skipped.

```php
public function beforeValidation(): void
{
    // Normalize attributes, hydrate computed fields, etc.
}

public function afterValidation(): void
{
    // Side effects that should only run when the model is valid.
}
```

### Validate on Specific Events

```php
protected static function booted(): void
{
    // Validate only on creating
    static::validateOnCreating();

    // Or validate on any custom event
    static::updating(function (Model $model): void {
        $model->validate();
    });
}
```

### Handling the Exceptions

Two exceptions can be thrown:

| Exception | Thrown when |
|-----------|-------------|
| `Illuminate\Validation\ValidationException` | A validation rule fails |
| `Opscale\Validations\Exceptions\MissingValidationRulesException` | The trait is used but no rules are declared on the model |

The first is the standard Laravel exception — your existing exception handler will format
it as a 422 JSON response automatically. The second is a `RuntimeException` and signals a
configuration bug; let it surface in development.

## Testing

The test suite uses [Pest](https://pestphp.com/) with [Orchestra Testbench](https://github.com/orchestral/testbench).

```bash
# Unit + Feature suites
npm test

# Individual suites
npm run test:unit
npm run test:feature

# Full pipeline: fix → refactor → lint → static analysis → test
npm run check
```

Static analysis runs at PHPStan **level 8** with the four `opscale-co/strict-rules` rule sets
(clean, ddd, smells, solid):

```bash
npm run analyse
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/opscale-co/.github/blob/main/CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email development@opscale.co instead of using the issue tracker.

## Credits

- [Opscale](https://github.com/opscale-co)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
