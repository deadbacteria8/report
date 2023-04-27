<?php

namespace App\Cards;

class Players
{
    public array $cards;
    public int $points;
    public int $playerNumber;
    public string $message;
    public $balance;
    public int $currentBet;
    public int $queueSpot;
    public bool $havePlayed;

    public function __construct(int $playerNumber, $balance)
    {
        $this->playerNumber = $playerNumber;
        $this->balance = $balance;
        $this->cards = [];
        $this->currentBet = 0;
        $this->queueSpot = 0;
        $this->havePlayed = false;
        $this->message = "";
        $this->points = 0;
    }
}
