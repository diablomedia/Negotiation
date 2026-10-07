<?php

namespace Negotiation\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Negotiation\EncodingNegotiator;

class EncodingNegotiatorTest extends TestCase
{
    /**
     * @var EncodingNegotiator
     */
    private $negotiator;

    protected function setUp(): void
    {
        $this->negotiator = new EncodingNegotiator();
    }

    public function testGetBestReturnsNullWithUnmatchedHeader(): void
    {
        $this->assertNull($this->negotiator->getBest('foo, bar, yo', ['baz']));
    }

    /**
     * @param list<string> $priorities
     */
    #[DataProvider('dataProviderForTestGetBest')]
    public function testGetBest(string $accept, array $priorities, ?string $expected): void
    {
        $accept = $this->negotiator->getBest($accept, $priorities);

        if (null === $accept) {
            $this->assertNull($expected);
        } else {
            $this->assertInstanceOf('Negotiation\AcceptEncoding', $accept);
            $this->assertEquals($expected, $accept->getValue());
        }
    }

    /**
     * @return list<array{string, list<string>, string|null}>
     */
    public static function dataProviderForTestGetBest(): array
    {
        return [
            ['gzip;q=1.0, identity; q=0.5, *;q=0', ['identity'], 'identity'],
            ['gzip;q=0.5, identity; q=0.5, *;q=0.7', ['bzip', 'foo'], 'bzip'],
            ['gzip;q=0.7, identity; q=0.5, *;q=0.7', ['gzip', 'foo'], 'gzip'],
            # Quality of source factors
            ['gzip;q=0.7,identity', ['identity;q=0.5', 'gzip;q=0.9'], 'gzip;q=0.9'],
        ];
    }

    public function testGetBestRespectsQualityOfSource(): void
    {
        $accept = $this->negotiator->getBest('gzip;q=0.7,identity', ['identity;q=0.5', 'gzip;q=0.9']);
        $this->assertInstanceOf('Negotiation\AcceptEncoding', $accept);
        $this->assertEquals('gzip', $accept->getType());
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('dataProviderForTestParseAcceptHeader')]
    public function testParseAcceptHeader(string $header, array $expected): void
    {
        $accepts = $this->call_private_method('Negotiation\Negotiator', 'parseHeader', $this->negotiator, [$header]);

        $this->assertSame($expected, $accepts);
    }

    /**
     * @return list<array{string, list<string>}>
     */
    public static function dataProviderForTestParseAcceptHeader(): array
    {
        return [
            ['gzip,deflate,sdch', ['gzip', 'deflate', 'sdch']],
            ["gzip, deflate\t,sdch", ['gzip', 'deflate', 'sdch']],
            ['gzip;q=1.0, identity; q=0.5, *;q=0', ['gzip;q=1.0', 'identity; q=0.5', '*;q=0']],
        ];
    }
}
