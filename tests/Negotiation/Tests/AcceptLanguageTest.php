<?php

namespace Negotiation\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Negotiation\AcceptLanguage;
use Negotiation\Exception\InvalidLanguage;

class AcceptLanguageTest extends TestCase
{
    #[DataProvider('dataProviderForGetType')]
    public function testGetType(?string $header, string $expected): void
    {
        $accept = new AcceptLanguage($header);
        $actual = $accept->getType();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return list<array{string|null, string}>
     */
    public static function dataProviderForGetType(): array
    {
        return [
            ['en;q=0.7', 'en'],
            ['en-GB;q=0.8', 'en-gb'],
            ['da', 'da'],
            ['en-gb;q=0.8', 'en-gb'],
            ['es;q=0.7', 'es'],
            ['fr ; q= 0.1', 'fr'],
            ['', ''],
            [null, ''],
        ];
    }

    #[DataProvider('dataProviderForGetSubPart')]
    public function testGetSubPart(string $header, ?string $expected): void
    {
        $accept = new AcceptLanguage($header);

        $this->assertSame($expected, $accept->getSubPart());
    }

    /**
     * @return list<array{string, string|null}>
     */
    public static function dataProviderForGetSubPart(): array
    {
        return [
            ['en', null],
            ['en-GB', 'gb'],
            ['zh-Hans-CN', 'cn'],
        ];
    }

    public function testInvalidLanguage(): void
    {
        $this->expectException(InvalidLanguage::class);

        new AcceptLanguage('en-Latn-US-extra');
    }

    #[DataProvider('dataProviderForGetValue')]
    public function testGetValue(string $header, string $expected): void
    {
        $accept = new AcceptLanguage($header);
        $actual = $accept->getValue();
        $this->assertEquals($expected, $actual);

    }

    /**
     * @return list<array{string, string}>
     */
    public static function dataProviderForGetValue(): array
    {
        return [
            ['en;q=0.7', 'en;q=0.7'],
            ['en-GB;q=0.8', 'en-GB;q=0.8'],
        ];
    }
}
