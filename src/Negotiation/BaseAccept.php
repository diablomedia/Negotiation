<?php

namespace Negotiation;

abstract class BaseAccept
{
    /**
     * @var float
     */
    private $quality = 1.0;

    /**
     * @var string
     */
    private $normalized;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @var array<string, string>
     */
    private $parameters;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string|null $value
     */
    public function __construct($value)
    {
        list($type, $parameters) = $this->parseParameters($value);

        if (isset($parameters['q'])) {
            $this->quality = (float) $parameters['q'];
            unset($parameters['q']);
        }

        $type = trim(strtolower($type));

        $this->value      = $value;
        $this->normalized = $type . ($parameters ? "; " . $this->buildParametersString($parameters) : '');
        $this->type       = $type;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getNormalizedValue()
    {
        return $this->normalized;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * @return array<string, string>
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @template T
     * @param string $key
     * @param T $default
     *
     * @return string|T
     */
    public function getParameter($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

    /**
     * @param string $key
     *
     * @return boolean
     */
    public function hasParameter($key)
    {
        return isset($this->parameters[$key]);
    }

    /**
     *
     * @param  string|null $acceptPart
     * @return array{string, array<string, string>}
     */
    private function parseParameters($acceptPart)
    {
        if ($acceptPart === null) {
            return ['', []];
        }

        $parts = explode(';', $acceptPart);
        $type  = array_shift($parts);

        $parameters = [];
        foreach ($parts as $part) {
            $part = explode('=', $part);

            if (2 !== count($part)) {
                continue; // TODO: throw exception here?
            }

            $key = strtolower(trim($part[0])); // TODO: technically not allowed space around "=". throw exception?
            $parameters[$key] = trim($part[1], ' "');
        }

        return [ $type, $parameters ];
    }

    /**
     * @param array<string, string> $parameters
     *
     * @return string
     */
    private function buildParametersString($parameters)
    {
        $parts = [];

        ksort($parameters);
        foreach ($parameters as $key => $val) {
            $parts[] = sprintf('%s=%s', $key, $val);
        }

        return implode('; ', $parts);
    }
}
