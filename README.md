# utility

A PHP utility project for exception classes and wrapper functions

## Benefits

PHP has an old core design dating back to its PHP 3 era. Examples include:

-   Using functions instead of classes.
-   Returning mixed types from functions.
-   Returning false, null, or empty array to supress errors.
-   Inconsistent function names and arguments.
-   Returning arrays or resources from functions instead of objects.
-   A mixture of mutable arguments and return values.

Even worse, the language introduced static typing and strict type checking tools.

-   Returning false|string doesn't make sense and should return ?string.
-   Type checkers force you to work harder to ignore errors using strict comparisons.

These utility classes wrap the PHP function calls and try to fix these problems.

## Function signatures

The functions were renamed for clarity. Function argument order was preserved, except:

-   Mutable arguments are now return values on getter functions.
-   If a signal value is returned, return null rather than false.
-   Arrays with multiple shapes and types were reduced to a single shape and type.

## Project setup

Standard config files for linting and testing are copied into place from a GitHub repository called
[config-setup](https://github.com/douglasgreen/config-setup). See that project's README page for
details.

## Todo

See [Todo](docs/todo.md).
