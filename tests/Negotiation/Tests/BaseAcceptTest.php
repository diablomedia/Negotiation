<?php

namespace Negotiation\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Negotiation\BaseAccept;

class BaseAcceptTest extends TestCase
{
    public function testNullValue(): void
    {
        $accept = new DummyAccept(null);

        $this->assertNull($accept->getValue());
        $this->assertSame('', $accept->getType());
        $this->assertSame('', $accept->getNormalizedValue());
        $this->assertSame([], $accept->getParameters());
        $this->assertSame(1.0, $accept->getQuality());
    }

    /**
     * @param array<string, float|int|string> $expected
     */
    #[DataProvider('dataProviderForParseParameters')]
    public function testParseParameters(string $value, array $expected): void
    {
        $accept     = new DummyAccept($value);
        $parameters = $accept->getParameters();

        // TODO: hack-ish... this is needed because logic in BaseAccept
        //constructor drops the quality from the parameter set.
        if (str_contains($value, 'q')) {
            $parameters['q'] = $accept->getQuality();
        }

        $this->assertCount(count($expected), $parameters);

        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $parameters);
            $this->assertEquals($value, $parameters[$key]);
        }
    }

    /**
     * @return list<array{string, array<string, float|int|string>}>
     */
    public static function dataProviderForParseParameters(): array
    {
        return [
            [
                'application/json ;q=1.0; level=2;foo= bar',
                [
                    'q' => 1.0,
                    'level' => 2,
                    'foo'   => 'bar',
                ],
            ],
            [
                'application/json ;q = 1.0; level = 2;     FOO  = bAr',
                [
                    'q' => 1.0,
                    'level' => 2,
                    'foo'   => 'bAr',
                ],
            ],
            [
                'application/json;q=1.0',
                [
                    'q' => 1.0,
                ],
            ],
            [
                'application/json;foo',
                [],
            ],
        ];
    }

    #[DataProvider('dataProviderBuildParametersString')]
    public function testBuildParametersString(string $value, string $expected): void
    {
        $accept = new DummyAccept($value);

        $this->assertEquals($expected, $accept->getNormalizedValue());
    }

    /**
     * @return list<array{string, string}>
     */
    public static function dataProviderBuildParametersString(): array
    {
        return [
            ['media/type; xxx = 1.0;level=2;foo=bar', 'media/type; foo=bar; level=2; xxx=1.0'],
        ];
    }
}

class DummyAccept extends BaseAccept {}
