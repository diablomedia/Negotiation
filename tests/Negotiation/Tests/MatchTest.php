<?php

namespace Negotiation\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Negotiation\AcceptMatch;

class MatchTest extends TestCase
{
    #[DataProvider('dataProviderForTestCompare')]
    public function testCompare(AcceptMatch $match1, AcceptMatch $match2, int $expected): void
    {
        $this->assertEquals($expected, AcceptMatch::compare($match1, $match2));
    }

    /**
     * @return list<array{AcceptMatch, AcceptMatch, int}>
     */
    public static function dataProviderForTestCompare(): array
    {
        return [
            [new AcceptMatch(1.0, 110, 1), new AcceptMatch(1.0, 111, 1),    0],
            [new AcceptMatch(0.1, 10, 1), new AcceptMatch(0.1, 10, 2),   -1],
            [new AcceptMatch(0.5, 110, 5), new AcceptMatch(0.5, 11, 4),    1],
            [new AcceptMatch(0.4, 110, 1), new AcceptMatch(0.6, 111, 3),    1],
            [new AcceptMatch(0.6, 110, 1), new AcceptMatch(0.4, 111, 3),   -1],
        ];
    }

    /**
     * @param array<int, AcceptMatch> $carry
     * @param array<int, AcceptMatch> $expected
     */
    #[DataProvider('dataProviderForTestReduce')]
    public function testReduce(array $carry, AcceptMatch $match, array $expected): void
    {
        $this->assertEquals($expected, AcceptMatch::reduce($carry, $match));
    }

    /**
     * @return list<array{array<int, AcceptMatch>, AcceptMatch, array<int, AcceptMatch>}>
     */
    public static function dataProviderForTestReduce(): array
    {
        return [
            [
                [1 => new AcceptMatch(1.0, 10, 1)],
                new AcceptMatch(0.5, 111, 1),
                [1 => new AcceptMatch(0.5, 111, 1)],
            ],
            [
                [1 => new AcceptMatch(1.0, 110, 1)],
                new AcceptMatch(0.5, 11, 1),
                [1 => new AcceptMatch(1.0, 110, 1)],
            ],
            [
                [0 => new AcceptMatch(1.0, 10, 1)],
                new AcceptMatch(0.5, 111, 1),
                [0 => new AcceptMatch(1.0, 10, 1), 1 => new AcceptMatch(0.5, 111, 1)],
            ],
        ];
    }
}
