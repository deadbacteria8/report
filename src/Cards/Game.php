<?php

namespace App\Cards;

use App\Cards\Players;
use App\Cards\Deck;
use App\Cards\Queue;

class Game
{
    /**
     * @var Players[]
     */
    public array $players;
    public Players $bankPlayer;
    public int $playerAmount;
    public Deck $deck;
    public bool $playersDone;
    public bool $gamePlaying;
    public Queue $queue;

    public function __construct(int $playerAmount)
    {
        $this->gamePlaying = false;
        $playerArray = array();
        $this->deck = new Deck();
        shuffle($this->deck->deck);
        $this->playerAmount = $playerAmount;
        for ($i = 0; $i < $playerAmount + 1; $i++) {
            $maxMoney = ($i == 0) ? INF : 1000;
            $playerArray[] = new Players($i, $maxMoney);
        }
        $this->bankPlayer = $playerArray[0];
        unset($playerArray[0]);
        $this->players = $playerArray;
        $this->queue = new Queue();
    }

    public function start(): void
    {
        if ($this->gamePlaying != false) {
            return;
        }
        $amount = count($this->players);
        $this->queue->createQueue($this->players);
        for ($i = 1; $i <= $amount; $i++) {
            $this->resetValues($this->players[$i]);
            $this->draw($this->players[$i], 2);
        }
        $this->resetValues($this->bankPlayer);
        $this->draw($this->bankPlayer, 2);
        $this->gamePlaying = true;
        $this->playersDone = false;
    }

    private function resetValues(Players $player): void
    {
        $player->points = 0;
        $player->cards = array();
        $player->message = "";
    }

    /**
     * @return int[]
     */
    public function getMoney(): array
    {
        $returnArray = array();
        foreach ($this->players as $player) {
            $returnArray[] = $player->balance;
        }
        return $returnArray;
    }

    public function bet(int $player, int $value): void
    {
        $currentPlayer = $this->players[$player];
        $currentPlayer->balance = $currentPlayer->balance - $value;
        $currentPlayer->currentBet = $value;
    }

    private function draw(Players $player, int $howMany): void
    {
        $this->newDeck($howMany);
        for ($i = 0; $i < $howMany; $i++) {
            $pulledCard = $this->deck->pullCard(1);
            $player->cards[] = $pulledCard;
        }
        $points = $this->getPoints($player);
        if ($points >= 21 && $player != $this->bankPlayer) {
            $player->message = ($points == 21) ? "BlackJack" : "Busted";
            $this->queue->changeQueuePositions($player, $this->players);
        }
    }

    private function newDeck(int $num): void
    {
        if ($num > $this->deck->arraylength()) {
            $this->deck = new Deck();
            shuffle($this->deck->deck);
        }
    }

    public function allPlayers(): array
    {
        $returnArray = [];
        foreach ($this->players as $player) {
            $returnArray[] = [
                "player-number" => $player->playerNumber,
                "cards" => $this->toList($player->cards),
                "Queue-Spot" => $player->queueSpot,
                "Points-Array" => $this->getPointsArray($player),
                "Player-Message" => $player->message];
        }
        return $returnArray;
    }

    /**
     * @return string[]
     */
    public function toList(array $cardsArray): array
    {
        $cards = [];

        foreach ($cardsArray as $cardArray) {
            foreach($cardArray as $card) {
                $cards[] = $card->getRanks() . $card->getSuits();
            }
        }
        return $cards;
    }


    private function gameEnd(): void
    {
        $queue = $this->queue->getQueue($this->players);
        $boolStrVariables = array_column($queue, 0);
        if (in_array(false, $boolStrVariables) == false) {
            $this->playersDone = true;
        }
        if ($this->playersDone == true) {
            while($this->getPoints($this->bankPlayer) < 17) {
                $this->draw($this->bankPlayer, 1);
            }
            $this->result();
            $this->gamePlaying = false;
        }
    }

    private function result(): void
    {
        $bankPoints = $this->getPoints($this->bankPlayer);
        $amount = count($this->players);
        for ($i = 1; $i <= $amount; $i++) {
            $currentPlayer = $this->players[$i];
            $playerPoints = $this->getPoints($currentPlayer);
            switch (true) {
                case ($playerPoints == $bankPoints && $playerPoints < 22):
                    $currentPlayer->message = "Pushed";
                    $currentPlayer->balance += $currentPlayer->currentBet;
                    break;
                case ($playerPoints == 21):
                    $currentPlayer->message = "You won " . ($currentPlayer->currentBet*2.5);
                    $currentPlayer->balance += $currentPlayer->currentBet*2.5;
                    break;
                case ($bankPoints <= 21 && $bankPoints >= $playerPoints) or ($playerPoints > 21):
                    $currentPlayer->message = "You lost " . $currentPlayer->currentBet;
                    break;
                default:
                    $currentPlayer->message = "You won " . $currentPlayer->currentBet*2;
                    $currentPlayer->balance += $currentPlayer->currentBet*2;
                    break;
            }
            $currentPlayer->currentBet = 0;
        }
    }

    public function makeAction(int $player, string $choice): void
    {
        if ($choice == "PullCard") {
            $this->draw($this->players[$player], 1);
        }
        if ($choice == "Stay") {
            $this->queue->changeQueuePositions($this->players[$player], $this->players);
        }
        $this->gameEnd();
    }

    /**
     * @return int[]
     */
    public function getPointsArray(Players $player): array
    {
        $cards = $player->cards;
        $points = [0];
        $aces = 0;
        foreach ($cards as $card) {
            $value = $card[0]->getValue();
            $points[0] += $value[0];
            if (count($value) == 2) {
                $aces += 1;
            }
        }

        if ($aces > 0 && $points[0]+10 <= 21) {
            $points = [$points[0]+10,$points[0]];
        }

        return $points;
    }

    /**
     * @return int
     */
    public function getPoints(Players $player)
    {
        $points = $this->getPointsArray($player);
        $player->points = $points[0];
        return $player->points;
    }
}
