# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Compatibility

- PHP: ^8.1|^8.2
- Laravel: ^9.0|^10.0|^11.0

## Commands

### Testing

```bash
# Run all tests
composer test

# Run tests with coverage report
composer test-coverage
```

### Static Analysis

```bash
# Run Psalm static analysis
composer psalm
```

### Code Formatting

```bash
# Fix code style issues
composer format
```

## Architecture Overview

This Laravel package provides custom attribute casters for working with JSON fields in Eloquent models. The main components are:

### Core Components

1. **Casts** (`src/Casts/`)
   - `AbstractMeta`: Base abstract class implementing Laravel's `CastsAttributes` interface
   - `SimpleJsonField`: Casts a JSON column to a single object
   - `ArrayOfJsonObjectsField`: Casts a JSON column to an array of objects
   - `FileJsonField`: Special cast for file-related JSON

2. **JSON Objects** (`src/Json/`)
   - `AbstractMeta`: Base class for JSON objects with utility methods
   - `JsonObject`: Foundation class for JSON data representation
   - `SimpleJsonField`: Object representation of JSON data
   - `ArrayOfJsonObjectsField`: Array representation of JSON objects

3. **Traits**
   - `HasDataArrayWithAttributes`: Core trait providing data access methods
   - `HasDateAttributes`: Adds date handling capabilities
   - `HasNumericAttributes`: Adds numeric operations (increment/decrement)
   - `HasMorphClassesAttributes`: Adds model relationship handling

### Usage Patterns

The package supports three main patterns:

1. **Out of the box casts**: Direct use of `SimpleJsonField` or `ArrayOfJsonObjectsField`
2. **Custom castable objects**: Creating custom casts that extend `AbstractMeta`
3. **Dynamic castable objects**: Dynamic class resolution based on model attributes

## Testing Approach

Tests are structured to verify:
- JSON field casting with different data types
- Attribute manipulation (get, set, remove)
- Date handling
- Numeric operations
- Array operations
- Morphing (model relationships)

Tests use SQLite in-memory database with Orchestra Testbench.