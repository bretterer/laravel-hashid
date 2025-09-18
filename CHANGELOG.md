# Changelog

All notable changes to `:package_name` will be documented in this file.

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
