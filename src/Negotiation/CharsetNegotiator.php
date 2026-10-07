<?php

namespace Negotiation;

/**
 * @extends AbstractNegotiator<AcceptCharset>
 */
class CharsetNegotiator extends AbstractNegotiator
{
    /**
     * {@inheritdoc}
     */
    protected function acceptFactory($accept)
    {
        return new AcceptCharset($accept);
    }
}
