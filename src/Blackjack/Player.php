<?php

namespace App\Blackjack;

class Player
{
    /**
     * @var Hand[]
     */
    public array $hands;
    public int $playerNumber;
    public int|float $balance;
    public BlackJack $owner;

    /**
     * @param int|float $balance
     * @param BlackJack $owner
     * construct method
     */
    public function __construct($balance, $owner)
    {
        $this->balance = $balance;
        $this->owner = $owner;
        $this->createHands();
    }

    public function createHands() : void 
    {
        $this->hands = [
            new Hand($this),
        ];
    }

    public function addHand() : void
    {
        if(!$this->owner->gamePlaying) {
        $this->hands[] = new Hand($this);
        }
    }
}
