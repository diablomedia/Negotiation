<?php

namespace Negotiation\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use Negotiation\CharsetNegotiator;

class CharsetNegotiatorTest extends TestCase
{
    /**
     * @var CharsetNegotiator
     */
    private $negotiator;

    protected function setUp(): void
    {
        $this->negotiator = new CharsetNegotiator();
    }

    public function testGetBestReturnsNullWithUnmatchedHeader(): void
    {
        $this->assertNull($this->negotiator->getBest('foo, bar, yo', ['baz']));
    }

    /**
     * 'bu' has the highest quality rating, but is non-existent,
     * so we expect the next highest rated 'fr' content to be returned.
     *
     * See: http://svn.apache.org/repos/asf/httpd/test/framework/trunk/t/modules/negotiation.t
     */
    public function testGetBestIgnoresNonExistentContent(): void
    {
        $acceptCharset = 'en; q=0.1, fr; q=0.4, bu; q=1.0';
        $accept        = $this->negotiator->getBest($acceptCharset, ['en', 'fr']);

        $this->assertInstanceOf('Negotiation\AcceptCharset', $accept);
        $this->assertEquals('fr', $accept->getValue());
    }

    /**
     * @param list<string> $priorities
     */
    #[DataProvider('dataProviderForTestGetBest')]
    public function testGetBest(string $accept, array $priorities, ?string $expected): void
    {
        if (is_null($expected)) {
            $this->expectException('Negotiation\Exception\InvalidArgument');
        }

        $accept = $this->negotiator->getBest($accept, $priorities);
        if (null === $accept) {
            $this->assertNull($expected);
        } else {
            $this->assertInstanceOf('Negotiation\AcceptCharset', $accept);
            $this->assertSame($expected, $accept->getValue());
        }
    }

    /**
     * @return list<array{string, list<string>, string|null}>
     */
    public static function dataProviderForTestGetBest(): array
    {
        $pearCharset  = 'ISO-8859-1, Big5;q=0.6,utf-8;q=0.7, *;q=0.5';
        $pearCharset2 = 'ISO-8859-1, Big5;q=0.6,utf-8;q=0.7';

        return [
            [$pearCharset, [ 'utf-8', 'big5', 'iso-8859-1', 'shift-jis',], 'iso-8859-1'],
            [$pearCharset, [ 'utf-8', 'big5', 'shift-jis',], 'utf-8'],
            [$pearCharset, [ 'Big5', 'shift-jis',], 'Big5'],
            [$pearCharset, [ 'shift-jis',], 'shift-jis'],
            [$pearCharset2, [ 'utf-8', 'big5', 'iso-8859-1', 'shift-jis',], 'iso-8859-1'],
            [$pearCharset2, [ 'utf-8', 'big5', 'shift-jis',], 'utf-8'],
            [$pearCharset2, [ 'Big5', 'shift-jis',], 'Big5'],
            ['utf-8;q=0.6,iso-8859-5;q=0.9', [ 'iso-8859-5', 'utf-8',], 'iso-8859-5'],
            ['', [ 'iso-8859-5', 'utf-8',], null],
            ['en, *;q=0.9', ['fr'], 'fr'],
            # Quality of source factors
            [$pearCharset, ['iso-8859-1;q=0.5', 'utf-8', 'utf-16;q=1.0'], 'utf-8'],
            [$pearCharset, ['iso-8859-1;q=0.8', 'utf-8', 'utf-16;q=1.0'], 'iso-8859-1;q=0.8'],
        ];
    }

    public function testGetBestRespectsPriorities(): void
    {
        $accept = $this->negotiator->getBest('foo, bar, yo', ['yo']);

        $this->assertInstanceOf('Negotiation\AcceptCharset', $accept);
        $this->assertEquals('yo', $accept->getValue());
    }

    public function testGetBestDoesNotMatchPriorities(): void
    {
        $acceptCharset = 'en, de';
        $priorities           = ['fr'];

        $this->assertNull($this->negotiator->getBest($acceptCharset, $priorities));
    }

    public function testGetBestRespectsQualityOfSource(): void
    {
        $accept = $this->negotiator->getBest('utf-8;q=0.5,iso-8859-1', ['iso-8859-1;q=0.3', 'utf-8;q=0.9', 'utf-16;q=1.0']);
        $this->assertInstanceOf('Negotiation\AcceptCharset', $accept);
        $this->assertEquals('utf-8', $accept->getType());
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('dataProviderForTestParseHeader')]
    public function testParseHeader(string $header, array $expected): void
    {
        $accepts = $this->call_private_method('Negotiation\CharsetNegotiator', 'parseHeader', $this->negotiator, [$header]);

        $this->assertSame($expected, $accepts);
    }

    /**
     * @return list<array{string, list<string>}>
     */
    public static function dataProviderForTestParseHeader(): array
    {
        return [
            ['*;q=0.3,ISO-8859-1,utf-8;q=0.7', ['*;q=0.3', 'ISO-8859-1', 'utf-8;q=0.7']],
            ['*;q=0.3,ISO-8859-1;q=0.7,utf-8;q=0.7', ['*;q=0.3', 'ISO-8859-1;q=0.7', 'utf-8;q=0.7']],
            ['*;q=0.3,utf-8;q=0.7,ISO-8859-1;q=0.7', ['*;q=0.3', 'utf-8;q=0.7', 'ISO-8859-1;q=0.7']],
            ['iso-8859-5, unicode-1-1;q=0.8', ['iso-8859-5', 'unicode-1-1;q=0.8']],
        ];
    }
}
