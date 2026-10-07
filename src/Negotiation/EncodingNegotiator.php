<?php

namespace Negotiation;

/**
 * @extends AbstractNegotiator<AcceptEncoding>
 */
class EncodingNegotiator extends AbstractNegotiator
{
    /**
     * {@inheritdoc}
     */
    protected function acceptFactory($accept)
    {
        return new AcceptEncoding($accept);
    }
}
