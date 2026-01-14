# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

Hi-Hat Repeater is a WordPress plugin that extends Advanced Custom Fields (ACF) with a custom field type for managing multiple text areas. It includes built-in GraphQL support for headless WordPress implementations.

## Common Development Commands

### Initial Setup
```bash
# Install PHP and Node dependencies
composer install
npm install
```

### Building & Assets
```bash
# Build minified CSS and JS for production
npm run build

# Watch mode for development (auto-rebuild on changes)
npm run watch

# Clean built assets
npm run clean
```

### Code Quality & Testing
```bash
# Run PHP CodeSniffer (WordPress coding standards)
composer run lint

# Auto-fix PHP coding standards issues
composer run lint:fix

# Run PHPStan static analysis (level 5)
composer run analyze

# Run PHPUnit tests
composer run test

# Run all checks (lint, analyze, test)
composer run check
```

### JavaScript & CSS Linting
```bash
# Lint CSS and JavaScript
npm run lint

# Auto-fix CSS and JavaScript
npm run lint:fix

# Individual linters
npm run lint:css       # stylelint
npm run lint:js        # eslint
```

### Single Test Execution
```bash
# Run a specific test file
composer run test -- tests/HiHatRepeaterFieldTest.php

# Run a specific test method
composer run test -- --filter test_field_name tests/HiHatRepeaterFieldTest.php
```

## Project Structure & Architecture

### Core Plugin Entry Point
**hi-hat-repeater.php** - Main plugin file that:
- Defines plugin metadata and constants
- Checks for ACF dependency
- Registers the custom field type on the `acf/include_field_types` hook
- Sets up GraphQL support via `graphql_register_types` hook

### ACF Field Implementation
**fields/class-hi-hat-repeater-field.php** - Custom ACF field class that extends `acf_field`:
- **render_field()** - Renders multiple textarea inputs with add/remove buttons
- **update_value()** - Cleans empty values from submitted array
- **input_admin_enqueue_scripts()** - Loads admin CSS and JavaScript assets
- The field stores data as a simple array of strings (no nested objects)

### Frontend Interactivity
**js/input.js** - jQuery-based field controller:
- Handles "Add" button to clone textarea items
- Handles "Remove" button with protection for single item
- Uses ACF's action hooks for field initialization and cloning

**css/input.css** - Admin field styling using flexbox layout

### GraphQL Integration
The plugin's `hi_hat_repeater_manual_graphql_registration()` function (in main file):
- Iterates through all ACF field groups and detects `hi_hat_repeater` fields
- Registers GraphQL fields on post types that have GraphQL support
- Field type is exposed as `[String]` (list of strings) in GraphQL schema
- Enables querying repeater values through WPGraphQL

### Testing
**tests/** - PHPUnit test suite:
- Tests field class properties (name, label, category)
- Tests `update_value()` method with various inputs (arrays, non-arrays, empty values)
- Bootstrap file loads the main plugin

## Key Configuration Files

### Code Standards (phpcs.xml)
- Uses WordPress Coding Standards and WordPress VIP Go rulesets
- PHP 7.4+ compatibility checks enabled
- Excludes vendor, node_modules, tests directories

### Static Analysis (phpstan.neon)
- Level 5 type checking
- Ignores ACF and WordPress functions not available during static analysis
- Checks fields/ and src/ directories

### CI/CD (.github/workflows/)
- **ci.yml** - Runs tests on PHP 7.4-8.2 with WordPress latest; deploys on main branch push
- **phpstan.yml** - Separate PHPStan analysis workflow on PHP 8.2

### Asset Processing
- **postcss.config.js** - PostCSS pipeline with preset-env, autoprefixer, and cssnano
- **package.json** - Node.js tools: ESLint, stylelint, terser, PostCSS

## Important Architectural Notes

1. **Simple Data Storage** - The field stores a flat array of strings. It does not support nested objects or sub-fields.

2. **ACF Hook Pattern** - Field registration happens on `acf/include_field_types` which fires after ACF is loaded. This is the standard ACF pattern.

3. **GraphQL Registration** - GraphQL fields are registered by scanning all field groups at runtime. This happens on every `graphql_register_types` hook, not at plugin activation.

4. **Admin Dependency** - The JavaScript interactivity and styling only load in the WordPress admin via `input_admin_enqueue_scripts()` method.

5. **Minimum Requirements** - PHP 7.4, WordPress 5.0, ACF 5.0. User's local rule specifies PHP 8.2 preference.

## Development Workflow Notes

- Run `composer run check` before committing to ensure all quality gates pass
- Built assets (minified CSS/JS) should be committed for production distributions
- Tests use PHPUnit without a test database; they test class behavior directly
- The plugin has no src/ directory yet, but composer.json is configured for PSR-4 autoloading if needed
