<?php

namespace Negotiation;

final class AcceptMatch
{
    /**
     * @param float $quality
     * @param int $score
     * @param int $index
     */
    public function __construct(public $quality, public $score, public $index) {}

    /**
     * @param AcceptMatch $a
     * @param AcceptMatch $b
     *
     * @return int
     */
    public static function compare(AcceptMatch $a, AcceptMatch $b)
    {
        if ($a->quality !== $b->quality) {
            return $a->quality > $b->quality ? -1 : 1;
        }

        if ($a->index !== $b->index) {
            return $a->index > $b->index ? 1 : -1;
        }

        return 0;
    }

    /**
     * @param array<int, AcceptMatch> $carry reduced array
     * @param AcceptMatch $match match to be reduced
     *
     * @return AcceptMatch[]
     */
    public static function reduce(array $carry, AcceptMatch $match)
    {
        if (!isset($carry[$match->index]) || $carry[$match->index]->score < $match->score) {
            $carry[$match->index] = $match;
        }

        return $carry;
    }
}
