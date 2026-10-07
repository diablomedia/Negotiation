<?php

namespace Negotiation\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Negotiation\Accept;

class AcceptTest extends TestCase
{
    public function testGetParameter(): void
    {
        $accept = new Accept('foo/bar; q=1; hello=world');

        $this->assertTrue($accept->hasParameter('hello'));
        $this->assertEquals('world', $accept->getParameter('hello'));
        $this->assertFalse($accept->hasParameter('unknown'));
        $this->assertNull($accept->getParameter('unknown'));
        $this->assertFalse($accept->getParameter('unknown', false));
        $this->assertSame('world', $accept->getParameter('hello', 'goodbye'));
    }

    #[DataProvider('dataProviderForTestGetNormalizedValue')]
    public function testGetNormalizedValue(string $header, string $expected): void
    {
        $accept = new Accept($header);
        $actual = $accept->getNormalizedValue();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return list<array{string, string}>
     */
    public static function dataProviderForTestGetNormalizedValue(): array
    {
        return [
            ['text/html; z=y; a=b; c=d', 'text/html; a=b; c=d; z=y'],
            ['application/pdf; q=1; param=p',  'application/pdf; param=p'],
        ];
    }

    #[DataProvider('dataProviderForGetType')]
    public function testGetType(string $header, string $expected): void
    {
        $accept = new Accept($header);
        $actual = $accept->getType();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return list<array{string, string}>
     */
    public static function dataProviderForGetType(): array
    {
        return [
            ['text/html;hello=world', 'text/html'],
            ['application/pdf', 'application/pdf'],
            ['application/xhtml+xml;q=0.9', 'application/xhtml+xml'],
            ['text/plain; q=0.5', 'text/plain'],
            ['text/html;level=2;q=0.4', 'text/html'],
            ['text/html ; level = 2   ; q = 0.4', 'text/html'],
            ['text/*', 'text/*'],
            ['text/* ;q=1 ;level=2', 'text/*'],
            ['*/*', '*/*'],
            ['*', '*/*'],
            ['*/* ; param=555', '*/*'],
            ['* ; param=555', '*/*'],
            ['TEXT/hTmL;leVel=2; Q=0.4', 'text/html'],
        ];
    }

    #[DataProvider('dataProviderForGetValue')]
    public function testGetValue(string $header, string $expected): void
    {
        $accept = new Accept($header);
        $actual = $accept->getValue();
        $this->assertEquals($expected, $actual);

    }

    /**
     * @return list<array{string, string}>
     */
    public static function dataProviderForGetValue(): array
    {
        return [
            ['text/html;hello=world  ;q=0.5', 'text/html;hello=world  ;q=0.5'],
            ['application/pdf', 'application/pdf'],
        ];
    }
}
