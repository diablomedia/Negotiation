<?php

namespace Negotiation;

/**
 * Common operations provided by all accept headers.
 *
 * @method string getNormalizedValue()
 * @method string|null getValue()
 * @method string getType()
 * @method float getQuality()
 * @method array<string, string> getParameters()
 * @method mixed getParameter(string $key, mixed $default = null)
 * @method bool hasParameter(string $key)
 */
interface AcceptHeader {}
