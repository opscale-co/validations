# Changelog

All notable changes to opscale-co/validations will be documented in this file.

## 2.0.0 (2026-11-06)

* feat(laravel)!: require Laravel 13, drop Laravel 11/12 ([4e2bf1c](https://github.com/opscale-co/validations/commit/4e2bf1c))
* feat(validatable)!: throw MissingValidationRulesException when trait has no rules ([43b2972](https://github.com/opscale-co/validations/commit/43b2972))
* Merge branch 'main' of https://github.com/opscale-co/validations ([441416d](https://github.com/opscale-co/validations/commit/441416d))
* docs(fix worng package name): fix worng package name ([9687faa](https://github.com/opscale-co/validations/commit/9687faa))
* docs(readme): document strict types, exception contract, and Pest commands ([cf9341b](https://github.com/opscale-co/validations/commit/cf9341b))
* test(validatable): add Pest unit and feature suites for trait and validator ([ed3bd09](https://github.com/opscale-co/validations/commit/ed3bd09))
* refactor(model-validator): declare strict_types and tighten type hints ([18d2834](https://github.com/opscale-co/validations/commit/18d2834))
* build(corrected package name): changed package name to opscale-co instead of just opscale ([6f4cae6](https://github.com/opscale-co/validations/commit/6f4cae6))
* build(deps): install Pest, Testbench, Larastan, strict-rules and Rector ([f20bc1f](https://github.com/opscale-co/validations/commit/f20bc1f))
* chore(speckit): bootstrap spec-kit scaffold and Opscale library constitution ([ad4b05b](https://github.com/opscale-co/validations/commit/ad4b05b))

### BREAKING CHANGE

* drops support for Laravel 5-12; now requires PHP 8.3+ and Laravel 13.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>
* Models that use Validatable but never declared rules used
to save silently; they now raise MissingValidationRulesException. Either
declare rules or stop using the trait on those models.

Co-Authored-By: Claude Opus 4.7 (1M context) <noreply@anthropic.com>

## 1.0.0 (2026-04-08)

* feat(project): working version ([080cafb](https://github.com/opscale-co/validations/commit/080cafb))
