<?php

namespace Negotiation\Tests;

use Exception;
use Negotiation\AcceptLanguage;
use Negotiation\Negotiator;
use Negotiation\AcceptHeader;
use PHPUnit\Framework\Attributes\DataProvider;
use Negotiation\Exception\InvalidArgument;
use Negotiation\LanguageNegotiator;

class LanguageNegotiatorTest extends TestCase
{
    /**
     * @var LanguageNegotiator
     */
    private $negotiator;

    protected function setUp(): void
    {
        $this->negotiator = new LanguageNegotiator();
    }

    /**
     * @param list<string> $priorities
     */
    #[DataProvider('dataProviderForTestGetBest')]
    public function testGetBest(string $accept, array $priorities, string|Exception|null $expected): void
    {
        try {
            $accept = $this->negotiator->getBest($accept, $priorities);

            if (null === $accept) {
                $this->assertNull($expected);
            } else {
                $this->assertInstanceOf(AcceptLanguage::class, $accept);
                $this->assertEquals($expected, $accept->getValue());
            }
        } catch (Exception $e) {
            $this->assertEquals($expected, $e);
        }
    }

    /**
     * @return list<array{string, list<string>, string|Exception|null}>
     */
    public static function dataProviderForTestGetBest(): array
    {
        return [
            ['en, de', ['fr'], null],
            ['foo, bar, yo', ['baz', 'biz'], null],
            ['fr-FR, en;q=0.8', ['en-US', 'de-DE'], 'en-US'],
            ['en, *;q=0.9', ['fr'], 'fr'],
            ['foo, bar, yo', ['yo'], 'yo'],
            ['en; q=0.1, fr; q=0.4, bu; q=1.0', ['en', 'fr'], 'fr'],
            ['en; q=0.1, fr; q=0.4, fu; q=0.9, de; q=0.2', ['en', 'fu'], 'fu'],
            ['', ['en', 'fu'], new InvalidArgument('The header string should not be empty.')],
            ['fr, zh-Hans-CN;q=0.3', ['fr'], 'fr'],
            # Quality of source factors
            ['en;q=0.5,de', ['de;q=0.3', 'en;q=0.9'], 'en;q=0.9'],
            # Generic fallback
            ['fr-FR, en-US;q=0.8', ['fr'], 'fr'],
            ['fr-FR, en-US;q=0.8', ['fr', 'en-US'], 'fr'],
            ['fr-FR, en-US;q=0.8', ['fr-CA', 'en'], 'en'],
        ];
    }

    public function testGetBestRespectsQualityOfSource(): void
    {
        $accept = $this->negotiator->getBest('en;q=0.5,de', ['de;q=0.3', 'en;q=0.9']);
        $this->assertInstanceOf(AcceptLanguage::class, $accept);
        $this->assertEquals('en', $accept->getType());
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('dataProviderForTestParseHeader')]
    public function testParseHeader(string $header, array $expected): void
    {
        $accepts = $this->call_private_method(Negotiator::class, 'parseHeader', $this->negotiator, [$header]);

        $this->assertSame($expected, $accepts);
    }

    /**
     * @return list<array{string, list<string>}>
     */
    public static function dataProviderForTestParseHeader(): array
    {
        return [
            ['en; q=0.1, fr; q=0.4, bu; q=1.0', ['en; q=0.1', 'fr; q=0.4', 'bu; q=1.0']],
            ['en; q=0.1, fr; q=0.4, fu; q=0.9, de; q=0.2', ['en; q=0.1', 'fr; q=0.4', 'fu; q=0.9', 'de; q=0.2']],
        ];
    }

    /**
     * Given a accept header containing specific languages (here 'en-US', 'fr-FR')
     *  And priorities containing a generic version of that language
     * Then the best language is mapped to the generic one here 'fr'
     */
    public function testSpecificLanguageAreMappedToGeneric(): void
    {
        $acceptLanguageHeader = 'fr-FR, en-US;q=0.8';
        $priorities           = ['fr'];

        $acceptHeader = $this->negotiator->getBest($acceptLanguageHeader, $priorities);

        $this->assertInstanceOf(AcceptHeader::class, $acceptHeader);
        $this->assertEquals('fr', $acceptHeader->getValue());
    }
}
