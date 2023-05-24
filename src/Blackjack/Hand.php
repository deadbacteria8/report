<?php

namespace App\Blackjack;

use App\Cards\Card;

class Hand
{
    /**
     * @var Card[][]
     */
    public array $cards;
    public int $points;
    public string $message;
    public int $currentBet;
    public int $queueSpot;
    public bool $havePlayed;
    public Player $owner;

    public function __construct(Player $owner)
    {
        $this->cards = [];
        $this->currentBet = 0;
        $this->queueSpot = 0;
        $this->havePlayed = false;
        $this->message = "";
        $this->points = 0;
        $this->owner = $owner;
    }
}
