# Opscale Project Constitution

**Project:** validations
**Project Type:** library
**Module Prefix:** validations
**Tenant Aware:** no
**Created:** 2026-05-01

> This constitution is the architectural DNA of every Opscale project.
> Claude Code MUST read and comply with it before generating any code, spec, plan, or task.
> It is derived from [opscale-co/strict-rules](https://github.com/opscale-co/strict-rules)
> and supersedes all other instructions. Deviations require explicit documentation.

---

## 0. Project Type

**Type: library**

`opscale-co/validations` is a pure utility library that adds Eloquent model validation
to Laravel applications via the `Validatable` trait and the `ModelValidator` class.
It has no domain, no Nova, no Actions, and no tenant scoping. It is consumed by other
Opscale modules and applications and published to Packagist.

### Library-type sequence

```
1. Write code (traits, services, helpers, rules, abstracts)
2. opscale-test    → configure Pest, PHPStan level 8, Duster, Rector
3. opscale-release → configure Semantic Release, CI/CD, SonarQube
```

Steps `opscale-process`, `opscale-dbml`, `opscale-bpmn`, `opscale-domain`, `opscale-ui`,
`opscale-logic`, `opscale-outputs`, and `opscale-ai` do NOT apply.
Quality gates still apply in full: PHPStan level 8, Duster, all tests pass, SonarQube.

---

## I. Architectural Philosophy

Opscale software is designed in a strict priority order. When there is a conflict between
levels, the higher level always wins.

**Priority 1 — Business (Information Flow)**
The system exists to model how the business works and move information correctly through it.
Business correctness always comes before implementation elegance.

**Priority 2 — End Users (Interface)**
Once the information flow is correct, the system must present it through interfaces that are
appropriate for each user type. UI decisions are secondary to domain decisions.

**Priority 3 — Technical Team (Maintainability)**
Code quality, patterns, and architecture conventions exist to serve the team maintaining the
system long term. SOLID principles are tools for keeping the codebase understandable and
extensible — they are means, not ends.

For a library like `validations`, Priority 1 collapses into "predictable, correct behavior
under every supported Laravel version" and Priority 3 dominates: SOLID, narrow public API,
zero hidden state.

---

## II. Strict Types — NON-NEGOTIABLE

**Every PHP file in this project MUST start with `declare(strict_types=1);`** immediately
after the opening `<?php` tag. No exceptions.

This applies to:
- All classes in `src/`
- All test files in `tests/`
- Configuration files that contain PHP class definitions
- Any helper, factory, stub, or fixture written in PHP

Additionally:
- Every method MUST declare parameter types and a return type. `mixed` is allowed only
  when the input is genuinely polymorphic (e.g., a Laravel attribute value).
- Class properties MUST be typed.
- `array` parameters and returns MUST carry a PHPDoc `@param array<...>` / `@return array<...>`
  generic so PHPStan level 8 can verify them.
- No suppression of strict-type errors via `@phpstan-ignore` without an inline reason.

CI fails if any `.php` file under `src/` or `tests/` is missing the `declare(strict_types=1);`
directive.

---

## III. Public API Surface

The library exposes exactly two public symbols:

| Symbol | Kind | Purpose |
|--------|------|---------|
| `Opscale\Validations\Validatable` | Trait | Hook validation into Eloquent model events |
| `Opscale\Validations\ModelValidator` | Class | Resolve and run validation against a model |

Anything else is internal. Adding a new public symbol requires a minor version bump and
a matching test that asserts the symbol is part of the contract.

### Backward compatibility

The library supports Laravel `^5.0|^6.20.26|^7.0|^8.0|^9.0|^10.0|^11.0|^12.0`.
- A change that requires a Laravel version newer than the lowest supported version is a
  breaking change and demands a major version bump.
- Method signatures on public symbols are frozen for the lifetime of a major version.

---

## IV. Validation Contract

The trait/class pair implements a four-step contract:

1. `beforeValidation()` (optional, defined on the model) runs first.
2. `ModelValidator->validate()` resolves rules, messages, attributes, and data.
3. Laravel's `Validator::make()` runs and throws `ValidationException` on failure.
4. `afterValidation()` (optional, defined on the model) runs last.

Rules can be:
- A static array `$validationRules` on the model class.
- A method `validationRules()` on the model instance.
- Per-context: `['create' => ..., 'update' => ...]` resolved from `$model->exists`.

Messages and attributes follow the same dual-source pattern (`$validationMessages` /
`validationMessages()`, `$validationAttributes` / `validationAttributes()`).

**Trait-level enforcement:** A model that uses the `Validatable` trait MUST declare validation
rules — either a public static `$validationRules` property or a `validationRules()` method.
If neither is present, `Validatable::validate()` throws
`Opscale\Validations\Exceptions\MissingValidationRulesException` before any callbacks run.
Using the trait is the explicit opt-in; forgetting to declare rules is a configuration error,
not a silent no-op.

**Validator-level behavior:** When `ModelValidator` is invoked directly (without the trait)
on a model with no rules, it returns silently. Direct use of the validator is a low-level
contract — the lenient behavior preserves backward compatibility for callers that intentionally
skip validation. The strict opt-in lives in the trait.

---

## V. SOLID Rules

All PHP files use `declare(strict_types=1)`. PHPStan runs at level 8.

**Single Responsibility**
`ModelValidator` does one thing: validate an Eloquent model against rules sourced from it.
It does not persist, transform, log, or notify. The `Validatable` trait does one thing:
wire validation into model lifecycle hooks.

**Open/Closed**
Models extend behavior by defining `beforeValidation` / `afterValidation` hooks, not by
modifying the trait or the validator.

**Liskov Substitution**
The validator accepts any `Illuminate\Database\Eloquent\Model` subclass without behavioral
surprises. A model that opts out of validation (no rules) MUST behave identically to one
without the trait.

**Interface Segregation**
The library defines no broad interfaces. The trait is opt-in; nothing forces a model to
implement methods it does not use.

**Dependency Inversion**
The validator receives its model via constructor injection. It uses Laravel's
`Validator` facade — replaceable in tests via the framework's bindings.

---

## VI. Multi-Tenancy

**Tenant Aware: no**

This library is infrastructure code consumed by both tenant-aware and single-tenant
applications. It MUST NOT introduce a `tenant_id` column requirement, scope queries by
tenant, or read from any tenant resolver. Tenant concerns belong in the consuming
application's models and repositories.

---

## VII. Code Quality Gates

No commit to `main` merges without all of the following passing:

1. ✅ PHPStan level 8 — zero errors
2. ✅ Duster lint — PHP clean
3. ✅ Rector — no pending refactors
4. ✅ Pest tests — all green across every supported Laravel/PHP combination in the matrix
5. ✅ Every `.php` file under `src/` and `tests/` declares `strict_types=1`
6. ✅ SonarQube quality gate — no new critical or blocker issues
7. ✅ Conventional Commits on every commit (enforced by commitlint + Husky)

---

## VIII. Testing Doctrine

Tests use **Pest** with **Orchestra Testbench** to boot a minimal Laravel application.

- **Unit tests** (`tests/Unit/`): exercise `ModelValidator` against in-memory model doubles
  with no database.
- **Feature tests** (`tests/Feature/`): exercise the `Validatable` trait through real
  Eloquent lifecycle events (`saving`, `creating`) on an SQLite in-memory connection.
- Every public branch of `ModelValidator::getRules`, `getMessages`, `getAttributes`, and
  `getData` is covered: method form, static-property form, and the absent-both fallback.
- Both validation phases (`create` vs `update`) are covered explicitly.
- The `beforeValidation` and `afterValidation` hooks are covered as opt-in callbacks.
- A failing rule MUST raise `Illuminate\Validation\ValidationException` — never a silent
  return.

---

## IX. Spec-Driven Development Sequence (library-mode)

```
opscale-init       → .specify/ scaffold + this constitution
[code lives in src/]
opscale-test       → tests/, phpunit.xml, phpstan.neon, rector.php, duster.json
opscale-release    → .releaserc.json, commitlint, Husky, GitHub Actions, SonarQube
```

No spec.md, data-model.md, process.md, plan.md, or tasks.md is produced for this
library. The README and the constitution together form the binding contract.

---

## Governance

- This constitution supersedes all other instructions, templates, and conventions.
- Any deviation requires explicit inline documentation with the business or technical reason.
- Amendments must propagate to all dependent `.specify/templates/` files.
- PRs violating any article are blocked until resolved — no exceptions.
