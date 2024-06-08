# utility

A PHP utility project for exception classes and wrapper functions

These utility classes exist because basic PHP functions return a mix of false or
null on failure rather than throwing exceptions which is tedious to deal with.

Other classes represent array shapes like the value returned by `parse_url`.
That makes it easier to validate URLs, pass them around as a unit, manipulate
them, and convert them back to strings.

## Function signatures

The functions were renamed for clarity. Function argument order was preserved,
except:

-   Mutable arguments are now return values on getter functions.
-   If a signal value is returned, return null rather than false.
-   Arrays with multiple shapes and types were reduced to a single shape and
    type.

## Setup

See [Project Setup Guide](docs/setup_guide.md).

## Todo

See [Todo](docs/todo.md).
