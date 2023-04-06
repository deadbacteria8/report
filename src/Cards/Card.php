<?php

namespace App\Cards;

class Card
{
    protected $value;
    protected $suit;
    protected $rank;

    public function __construct($value, $suit, $rank)
    {
        $this->value = $value;
        $this->suit = $suit;
        $this->rank = $rank;
    }

    public function getRanks(): string
    {
        return $this->rank;
    }

    public function getSuits(): string
    {
        return $this->suit;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
