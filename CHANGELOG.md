# Changelog

All notable changes to `:package_name` will be documented in this file.

## v0.3.1 - 2026-03-25

### Fixed

- Changed default column length from `CHAR(24)` to `CHAR(16)` in `hashId`, `foreignHashId`, `hashIdMorphs`, and `nullableHashIdMorphs` macros to match the actual generated HashId length (#7)
- Fixes PostgreSQL padding issue where `CHAR(24)` columns returned space-padded values for 16-character HashIds

## v0.3.0 - 2026-03-23

### What's New

#### UUID/ULID Feature Parity

- **Configurable HashId length** — Override `hashIdLength()` per-model instead of hardcoded 16
- **Testing fakes** — `LaravelHashId::freeze()`, `createUsing()`, `createUsingSequence()`, `createNormally()`
- **Polymorphic morphs** — `$table->hashIdMorphs()` and `$table->nullableHashIdMorphs()` Blueprint macros
- **Validation rule** — `hashid` and `hashid:16` for form requests
- **Str macros** — `Str::hashId()` and `Str::isHashId()` helpers
- **Static validation** — `LaravelHashId::isValid()` helper

#### CI Improvements

- Workflows now trigger on `pull_request` instead of every push
- Bumped `actions/checkout` to v6, `git-auto-commit-action` to v7, `ramsey/composer-install` to v4

**Full Changelog**: https://github.com/bretterer/laravel-hashid/compare/v0.2.0...v0.3.0

## v0.2.0 - 2026-03-19

### What's New

- Add Laravel 13 support

**Full Changelog**: https://github.com/bretterer/laravel-hashid/compare/v0.1.2...v0.2.0

## Laravel Hashid v0.1.2 - 2025-09-27

### Fixes

- `foreignHashId` no longer requires table name

**Full Changelog**: https://github.com/bretterer/laravel-hashid/compare/v0.1.1...v0.1.2

## Laravel Hashid v0.1.1 - 2025-09-27

### Removed

- spatie/laravel-package-tools
- spatie/laravel-ray

**Full Changelog**: https://github.com/bretterer/laravel-hashid/compare/v0.1.0...v0.1.1

## Laravel Hashid v0.1.0 - 2025-09-18

### Added

- Initial release of Laravel Hashid.
- Collision-resistant, base62 HashIds for Eloquent models.
- HasHashIds trait for automatic HashId assignment.
- hashId and foreignHashId migration columns for primary and foreign keys.
- Custom prefix support for HashIds.
- HashId generator for random and value-based IDs.
- Validation for HashId format and uniqueness.
- Pest tests for model, migration, and generator logic.

### Credits

- Developed by [Brian Retterer](https://github.com/bretterer)
- Special thanks to [Spatie](https://spatie.be/open-source) for their Laravel package skeleton.
