<?php

namespace App\Cards;

use App\Cards\Deck;

class Deck
{
    /**
     * @var Card[]
     */
    public array $deck = [];

    public function __construct()
    {
        $ranks = ["2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K", "A"];
        $suits = ["♠", "♣", "♥", "♦"];

        foreach ($suits as $suit) {
            foreach ($ranks as $rank) {
                $value = ($rank === "J" || $rank === "Q" || $rank === "K") ? [10] : ($rank === "A" ? [1, 11] : [intval($rank)]);
                $card = new Card($value, $suit, $rank);
                $this->add($card);
            }
        }
    }

    public function add(Card $card): void
    {
        $this->deck[] = $card;
    }

    /**
     * @param Card[] $deck
     * @return string[]
     */
    public function toList(array $deck): array
    {
        $cards = [];

        foreach ($deck as $card) {
            $cards[] = $card->getRanks() . $card->getSuits();
        }

        return $cards;
    }

    /**
     * @return Card[]
     */
    public function getDeck(): array
    {
        return $this->deck;
    }

    /**
     * @param int $num
     * @return Card[]
     */
    public function pullCard(int $num): array
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
