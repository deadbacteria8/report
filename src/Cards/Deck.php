<?php

namespace App\Cards;

use App\Cards\Deck;

class Deck
{
    private $deck = [];

    public function __construct()
    {
        $ranks = ["2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K", "A"];
        $suits = ["♠", "♣", "♥", "♦"];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $value = 0;
                if ($rank === "J" || $rank === "Q" || $rank === "K") {
                    $value = 10;
                } elseif ($rank === "A") {
                    $value = 11;
                } else {
                    $value = intval($rank);
                }
                $card = new Card($value, $suit, $rank);
                $this->add($card);
            }
        }
    }

    public function add(Card $card): void
    {
        $this->deck[] = $card;
    }

    public function toList($deck): array
    {
        $cards = [];

        foreach ($deck as $card) {
            $cards[] = $card->getRanks() . $card->getSuits();
        }

        return $cards;
    }

    public function getDeck(): array
    {
        return $this->deck;
    }

    public function pullCard($num): array
    {
        $deck = $this->deck;
        $rand = array_rand($deck, $num);
        if ($num == 1) {
            $rand = [$rand];
        }
        $returnArray = [];
        shuffle($rand);
        foreach ($rand as $card) {
            $returnArray[] = $deck[$card];
            unset($deck[$card]);
        };
        $this->deck = $deck;
        return $returnArray;
    }

    public function arraylength(): int
    {
        $deck = $this->deck;
        return count($deck);
    }
}
