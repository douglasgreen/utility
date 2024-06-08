# Project Setup Guide

## Setup Scripts

This project uses the
[GitLab script system](https://github.blog/2015-06-30-scripts-to-rule-them-all/).

To install project dependencies, run `script/bootstrap`.

To set up the project, run `script/setup`.

To lint the project, run `script/lint`.

To test the project, run `script/test`.

## Linting, Fixing, and Testing

### PHP

-   Lint: `composer lint`
-   Fix: `composer lint:fix`
-   Test: `composer test`

### JavaScript

-   Fix: `npm run lint:fix`

### Fixing PHP

When using prettier with `@prettier/plugin-php`, PHP is being reformatted with
`npm run lint:fix` and with `composer lint:fix`. You should run
`npm run lint:fix` first and let `composer lint:fix` clean up afterward.

Currently `@prettier/plugin-php` only supports up to PHP 8.2 so it may give up
with some syntax errors.

## Husky Hooks

Linting and testing are automatically run by `.husky/pre-commit`. Fix any errors
or use `--no-verify` to bypass the check.

Project setup is automatically run by `.husky/post-checkout` and
`.husky/post-merge`. That updates your Composer and NPM dependencies in case
your dependencies were changed by incoming code.

[Conventional Commits](https://www.npmjs.com/package/@commitlint/config-conventional)
are enforced by `.husky/commit-msg`. Fix any commit message errors before
committing.
