# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Commands

### Testing
- `composer test` - Run all tests using Pest
- `composer test-coverage` - Run tests with coverage report

### Code Quality
- `composer analyse` - Run PHPStan static analysis (level 5)
- `composer format` - Format code using Laravel Pint

### Development
- `composer prepare` - Discover packages for Testbench (runs automatically after autoload dump)

## Architecture Overview

This is a Laravel package that provides HashId functionality for Eloquent models, similar to native ULIDs and UUIDs.

### Core Components

**LaravelHashId Class** (`src/LaravelHashId.php`)
- Main generator class with two methods:
  - `generate(int $length = 16)` - Creates random collision-resistant HashIds
  - `generateFromValue($value, ?string $salt = null, int $length = 16)` - Creates HashIds from existing values
- Uses base62 encoding (0-9, A-Z, a-z) for URL-safe, compact IDs
- Relies on GMP extension for large number operations

**HasHashIds Trait** (`src/Traits/HasHashIds.php`)
- Implements Laravel's `HasUniqueStringIds` concern
- Provides `newUniqueId()` method that generates 16-character base62 HashIds
- Supports optional prefixes via `idPrefix()` method (format: `prefix_hashid`)
- Validates HashIds are exactly 16 characters and match base62 pattern

**Service Provider** (`src/LaravelHashIdServiceProvider.php`)
- Registers the package with Laravel using Spatie's package tools
- Auto-discovery enabled via composer.json

**Facade** (`src/Facades/LaravelHashId.php`)
- Provides static access to LaravelHashId functionality

### Database Schema Extensions

The package extends Laravel's Schema builder to add HashId column types:
- `$table->hashId('column', length)` - Creates HashId columns
- `$table->foreignHashId('column', 'table', 'referenced_column', length)` - Creates foreign key HashId columns

### Testing Setup

Uses Pest PHP with these key test files:
- `HashIdTest.php` - Core HashId generation functionality
- `FacadeTest.php` - Facade functionality
- `HasHashIdsExtraTest.php` - Trait behavior with models
- `ArchTest.php` - Architecture constraints

Test environment uses Orchestra Testbench for Laravel package testing. The package supports Laravel 11+ and Laravel 12.8+ (Orchestra Testbench v10.x requires Laravel 12.8.0 minimum). GitHub Actions excludes Laravel 12 + prefer-lowest due to this incompatibility.

## Package Structure

- Namespace: `Bretterer\LaravelHashId`
- PSR-4 autoloading from `src/`
- Requires PHP 8.3+ and Laravel 11+
- Uses GMP extension for cryptographic operations