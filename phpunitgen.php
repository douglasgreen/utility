<?php

declare(strict_types=1);

use PhpUnitGen\Core\Generators\Tests\DelegateTestGenerator;

return [
    /*
     * Tells if the generator should overwrite existing files with generated
     * tests files.
     */
    'overwriteFiles' => false,

    /*
     * Only files matching this regex will have tests generation. This must be
     * an array of RegExp compatible with "preg_match", but without the
     * opening and closing "/", as they will be added automatically.
     */
    'includedFiles' => ['\.php$'],

    /*
     * Tells if calling a framework "make" command should call PhpUnitGen to
     * generate the associated tests. Only works with Laravel for the moment.
     */
    'generateOnMake' => true,

    /*
     * Tells if the generator should create tested class instantiation and
     * complex tests skeleton (getter/setter tests...).
     */
    'automaticGeneration' => true,

    /*
     * Tells which implementation you want to use when PhpUnitGen requires a
     * specific contract. Please see
     * https://phpunitgen.io/docs#/en/configuration?id=implementations-to-use
     */
    'implementations' => DelegateTestGenerator::implementations(),

    /*
     * This string will be prepend to the test class namespace.
     */
    'baseTestNamespace' => 'Tests',

    /*
     * The absolute class name to TestCase.
     */
    'testCase' => 'Tests\TestCase',

    /*
     * Tells if the test class should be final.
     */
    'testClassFinal' => false,

    /*
     * Tells if the test class should declare strict types.
     */
    'testClassStrictTypes' => true,

    /*
     * Tells if the test class properties should be typed or documented.
     */
    'testClassTypedProperties' => true,

    /*
     * Those methods will not have tests or skeleton generation. This must be an
     * array of RegExp compatible with "preg_match", but without the opening and
     * closing "/", as they will be added automatically.
     */
    'excludedMethods' => ['__construct', '__destruct'],

    /*
     * The documentation header to append to generated files.  Should be a full
     * documentation content (with lines breaks, opening tags, etc.) or an empty
     * string to disable printing a documentation header.
     */
    'phpHeaderDoc' => '',
];
