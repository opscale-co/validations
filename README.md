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

## Installation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/opscale/validations.svg?style=flat-square)](https://packagist.org/packages/opscale/validations)

You can install the package via composer:

```bash

composer require opscale/validations

```

## Usage

Here the User model is mentioned as an example. You can use this in any model you want.

### Basic Setup

```php

use Opscale\Validations\Validatable;

class User extends Model
{
    use Validatable;

    public static $validationRules = [
        'name' => 'required|max:10',
        'email' => 'required|email',
    ];

    public static function boot()
    {
        parent::boot();

        // Validate the model on saving
        static::validateOnSaving();
    }
}

```

### Defining Rules, Messages, and Attributes

Rules, messages, and attributes can all be defined as **static properties** or **instance methods**. Methods take priority over properties and offer more flexibility when you need dynamic logic.

#### As static properties

```php

public static $validationRules = [
    'name' => 'required|max:10',
    'email' => 'required|email',
];

public static $validationMessages = [
    'name.required' => 'Name field is required.',
    'email.email' => 'The given email is in invalid format.',
];

public static $validationAttributes = [
    'name' => 'User Name',
];

```

#### As instance methods

```php

public function validationRules()
{
    return [
        'name' => 'required|max:10',
        'email' => 'required|email',
    ];
}

public function validationMessages()
{
    return [
        'name.required' => 'Name field is required.',
        'email.email' => 'The given email is in invalid format.',
    ];
}

public function validationAttributes()
{
    return [
        'name' => 'User Name',
    ];
}

```

### Context-Aware Rules (Create vs Update)

You can define different rules for create and update operations:

```php

public static $validationRules = [
    'name' => 'required|max:10',
    'email' => [
        'create' => 'required|email|unique:users',
        'update' => 'required|email',
    ],
];

```

### Control the Data Being Validated

You can control the data which gets validated by adding a `validationData` method:

```php

public function validationData(array $data)
{
    $data["name"] = strtolower($data["name"]);

    return $data;
}

```

### Before and After Validation Hooks

```php

public function beforeValidation()
{
    // Code to execute before validation
}

public function afterValidation()
{
    // Code to execute after validation
}

```

### Validate on Specific Events

```php

public static function boot()
{
    parent::boot();

    // Validate only on creating
    self::validateOnCreating();

    // Or validate on any custom event
    self::creating(function ($model) {
        $model->validate();
    });
}

```

## Testing

``` bash

composer test

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
