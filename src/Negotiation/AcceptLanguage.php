<?php

namespace Negotiation;

use Negotiation\Exception\InvalidLanguage;

final class AcceptLanguage extends BaseAccept implements AcceptHeader
{
    /** @var string */
    private $language;
    /** @var string|null */
    private $region;

    public function __construct($value)
    {
        parent::__construct($value);

        $parts = explode('-', $this->type);

        if (2 === count($parts)) {
            $this->language = $parts[0];
            $this->region   = $parts[1];
        } elseif (1 === count($parts)) {
            $this->language = $parts[0];
        } elseif (3 === count($parts)) {
            $this->language = $parts[0];
            $this->region   = $parts[2];
        } else {
            throw new InvalidLanguage();
        }
    }

    /**
     * @return string|null
     */
    public function getSubPart()
    {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getBasePart()
    {
        return $this->language;
    }
}
