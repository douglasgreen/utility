<?php

declare(strict_types=1);

namespace DouglasGreen\Tests\Utility\FileSystem;

use DouglasGreen\Utility\Data\FlagChecker;
use DouglasGreen\Utility\FileSystem\FileException;
use DouglasGreen\Utility\FileSystem\Search;
use phpmock\Mock;
use phpmock\MockBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{
    protected ?Mock $globMock = null;

    public static function flagCombinationsProvider(): \Iterator
    {
        yield 'No flags' => [0, 0];
        yield 'ADD_SLASH' => [Search::ADD_SLASH, GLOB_MARK];
        yield 'NO_ESCAPE' => [Search::NO_ESCAPE, GLOB_NOESCAPE];
        yield 'NO_SORT' => [Search::NO_SORT, GLOB_NOSORT];
        yield 'ONLY_DIRS' => [Search::ONLY_DIRS, GLOB_ONLYDIR];
        yield 'STOP_ON_ERROR' => [Search::STOP_ON_ERROR, GLOB_ERR];
        yield 'All flags' => [
            Search::ADD_SLASH | Search::NO_ESCAPE | Search::NO_SORT | Search::ONLY_DIRS | Search::STOP_ON_ERROR,
            GLOB_MARK | GLOB_NOESCAPE | GLOB_NOSORT | GLOB_ONLYDIR | GLOB_ERR,
        ];
    }

    /**
     * The tearDown method is called after each test.
     */
    protected function tearDown(): void
    {
        // Disable the mock if it has been created.
        if ($this->globMock instanceof Mock) {
            $this->globMock->disable();
        }

        parent::tearDown();
    }

    #[DataProvider('flagCombinationsProvider')]
    public function testFindAllWithDifferentFlags(int $flags, int $expectedPhpFlags): void
    {
        $pattern = '*.txt';
        $expectedResult = ['file1.txt', 'file2.txt'];

        $this->mockGlob(function ($p, $f) use (
            $pattern,
            $expectedPhpFlags,
            $expectedResult
        ): array {
            $this->assertSame($pattern, $p);
            $this->assertSame($expectedPhpFlags, $f);
            return $expectedResult;
        });

        $result = Search::findAll($pattern, $flags);
        $this->assertSame($expectedResult, $result);
    }

    public function testFindAllThrowsExceptionOnGlobFailure(): void
    {
        $pattern = '*.txt';

        $this->mockGlob(fn(): bool => false);

        $this->expectException(FileException::class);
        $this->expectExceptionMessage('Unable to search files for pattern "*.txt"');

        Search::findAll($pattern);
    }

    public function testGetFlagChecker(): void
    {
        $flagChecker = Search::getFlagChecker(Search::ADD_SLASH | Search::NO_ESCAPE);
        $this->assertInstanceOf(FlagChecker::class, $flagChecker);
        $this->assertTrue($flagChecker->get('addSlash'));
        $this->assertTrue($flagChecker->get('noEscape'));
        $this->assertFalse($flagChecker->get('noSort'));
        $this->assertFalse($flagChecker->get('onlyDirs'));
        $this->assertFalse($flagChecker->get('stopOnError'));
    }

    protected function mockGlob(callable $callback): void
    {
        $builder = new MockBuilder();
        $builder->setNamespace('DouglasGreen\Utility\FileSystem')
            ->setName('glob')
            ->setFunction($callback);
        $this->globMock = $builder->build();
        $this->globMock->enable();
    }
}
