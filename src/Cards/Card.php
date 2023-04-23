<?php

namespace App\Cards;

class Card
{
    /**
     * @var int[]
     */
    protected array $value;
    protected string $suit;
    protected string $rank;

    /**
     * @param int[] $value
     * @param string $suit
     * @param string $rank
     */
    public function __construct(array $value, string $suit, string $rank)
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

    /**
     * @return int[] $value
     */
    public function getValue(): array
    {
        return $this->value;
    }
}
