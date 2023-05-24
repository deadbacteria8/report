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


    /**
     * @param int $playerAmount
     * Creates gameclass with Players instances.
     */
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
        if (isset($playerArray[0])) {
            unset($playerArray[0]);
            $playerArray = array_values($playerArray);
        }
        $this->players = $playerArray;
        $this->queue = new Queue();
        $this->playersDone = false;
    }

    /**
     * starting function. Starting the game with calling other methods such as createQueue, resetValues and draw.
     */
    public function start(): void
    {
        if ($this->gamePlaying === false) {
            $amount = count($this->players);
            $this->queue->createQueue($this->players);
            $this->gamePlaying = true;
            $this->playersDone = false;
            for ($i = 0; $i < $amount; $i++) {
                $this->resetValues($this->players[$i]);
                $this->draw($this->players[$i], 2);
            }
            $this->resetValues($this->bankPlayer);
            $this->draw($this->bankPlayer, 2);
        }
    }

    /**
     * @param Players $player
     * resets values for $player.
     */
    private function resetValues(Players $player): void
    {
        $player->points = 0;
        $player->cards = array();
        $player->message = "";
    }

    /**
     * @param int $player
     * @param int $value
     *  places bet for chosen player. bet function is called in the GameController with $player value retrieved from html-form-request.
     */
    public function bet(int $player, int $value): void
    {
        $currentPlayer = $this->players[$player];
        $currentPlayer->balance = $currentPlayer->balance - $value;
        $currentPlayer->currentBet = $value;
    }

    /**
     * @param Players $player
     * @param int $howMany
     *  function draws card depending on the value of $howMany. Then it adds the points to $player and checks if the player has blackjack or has busted.
     */
    private function draw(Players $player, int $howMany): void
    {
        $this->newDeck($howMany);
        for ($i = 0; $i < $howMany; $i++) {
            $pulledCard = $this->deck->pullCard(1);
            $player->cards[] = $pulledCard;
        }
        $this->addPoints($player);
        if ($player->points >= 21 && $player != $this->bankPlayer) {
            $player->message = ($player->points == 21) ? "BlackJack" : "Busted";
            $this->queue->changeQueuePositions($player, $this->players);
            $this->gameEnd();
        }
    }

    /**
     * @param int $num
     * checks if the array has enough cards left, if not, a new deck will be created and shuffled
     */
    private function newDeck(int $num): void
    {
        if ($num > $this->deck->arraylength()) {
            $this->deck = new Deck();
            shuffle($this->deck->deck);
        }
    }

    /**
     * gathering all the values for each players and returns it as an array.
     * @return array[][]
     */
    public function allPlayers(): array
    {
        $returnArray = [];
        foreach ($this->players as $player) {
            $returnArray[] = [
                "player-number" => $player->playerNumber,
                "cards" => $this->toList($player->cards),
                "Queue-Spot" => $player->queueSpot,
                "Points-Array" => $this->getPointsArray($player),
                "Money" => $player->balance,
                "Player-Message" => $player->message];
        }
        return $returnArray;
    }

    /**
     * @param Card[][] $cardsArray An array of arrays containing Card objects.
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

    /**
     * Calling getQueue method to determine if all players have played, if so the bankPlayer will draw cards and then the result() method is called.
     */
    private function gameEnd(): void
    {
        $queue = $this->queue->getQueue($this->players);
        $boolStrVariables = array_column($queue, 0);
        if (in_array(false, $boolStrVariables) === false) {
            $this->playersDone = true;
        }
        if ($this->playersDone === true) {
            while($this->bankPlayer->points < 17) {
                $this->draw($this->bankPlayer, 1);
            }
            $this->result();
            $this->gamePlaying = false;
        }
    }

    /**
     * result function retrieves bankplayer points and compares it to all the player points to determine if the players balance should be increased depending on the result and bet made.
     */
    private function result(): void
    {
        $bankPoints = $this->bankPlayer->points;
        $amount = count($this->players);
        for ($i = 0; $i < $amount; $i++) {
            $currentPlayer = $this->players[$i];
            $playerPoints = $currentPlayer->points;
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

    /**
     * @param int $player
     * @param string $choice
     * method makes action for chosen player. The method is called in the gamecontroller with the value of the submitted html-form(player-number).
     */
    public function makeAction(int $player, string $choice): void
    {
        if ($choice == "PullCard") {
            $this->draw($this->players[$player], 1);
        }
        if ($choice == "Stay") {
            $this->queue->changeQueuePositions($this->players[$player], $this->players);
            $this->gameEnd();
        }
    }

    /**
     * @return int[]
     * method returns array with possible point-outcomes. This method is crucial because Ace can be two different values.
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
     * @param Players $player
     * method updates the player-score with the highest possible value.
     */
    public function addPoints(Players $player): void
    {
        $points = $this->getPointsArray($player);
        $player->points = $points[0];
    }
}
