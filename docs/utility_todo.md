# Todo

-   Write docs for each function replacement
-   Write a bin script to find functions to replace and print help for each
-   Finish utility dirs
-   Write unit tests
-   Add more wrappers for superglobals
-   Support `PREG_SET_ORDER` for `preg_match_all`.
-   Support `PREG_SPLIT_OFFSET_CAPTURE` for `preg_split`.
-   Support `PREG_OFFSET_CAPTURE` for `preg_replace_callback`.

## Functions to write

See https://www.php.net/manual/en/function.fsockopen.php.

-   fsockopen - returns resource
-   The other functions don't share data and should be static functions on a Network class.

## Documentation

Write documentation with examples.

## Finder

Write a function finder to find and replace.

## Static

Swap static and non-static functions.
