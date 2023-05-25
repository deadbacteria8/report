<?php

namespace App\Blackjack;

use App\Blackjack\Player;
use App\Cards\Deck;
use App\Cards\Queue;
use App\Blackjack\Hand;

class BlackJack
{
    public Player $player;
    public Player $bankPlayer;
    public Deck $deck;
    public bool $playerDone;
    public bool $gamePlaying;
    public Queue $queue;


    /**
     * Creates instance.
     */
    public function __construct()
    {
        $this->gamePlaying = false;
        $this->deck = new Deck();
        shuffle($this->deck->deck);
        $this->bankPlayer = new Player(INF, $this);
        $this->player = new Player(1000, $this);
        $this->queue = new Queue();
        $this->playerDone = false;
    }

    /**
     * starting function. Starting the game with calling other methods such as createQueue and draw.
     */
    public function start(): void
    {
        if ($this->gamePlaying === false) {
            $this->queue->createQueue($this->player->hands);
            $this->playerDone = false;
            $this->gamePlaying = true;
            $playerHands = $this->player->hands;
            array_unshift($playerHands,$this->bankPlayer->hands[0]);
            $amount = count($this->bankPlayer->hands[0]->cards);
            while ($amount < 2) {
                foreach ($playerHands as $hand) {
                    $this->draw($hand, 1);
                }
                $amount = count($this->bankPlayer->hands[0]->cards);
            }
        }
    }

    /**
     *  resets values
     */
    public function reset(): void
    {
        $this->player->createHands();
        $this->bankPlayer->createHands();
        $this->gamePlaying = false;
    }

    /**
     * @param Hand $hand
     * @param int $value
     *  places bet for chosen hand.
     */
    public function bet(Hand $hand, int $value): void
    {
        if(!$this->gamePlaying) {
            $this->player->balance = $this->player->balance - $value;
            $hand->currentBet = $value;
        }
    }

    /**
     * @param Hand $hand
     * @param int $howMany
     *  function draws card depending on the value of $howMany. Then it adds the points to $hand and checks if the hand has blackjack or has busted.
     */
    private function draw(Hand $hand, int $howMany): void
    {
        $this->newDeck($howMany);
        for ($i = 0; $i < $howMany; $i++) {
            $pulledCard = $this->deck->pullCard(1);
            $hand->cards[] = $pulledCard;
        }
        $this->addPoints($hand);
        if ($hand->points >= 21 && $hand->owner !== $this->bankPlayer) {
            $hand->message = ($hand->points == 21) ? "BlackJack" : "Busted";
            $this->queue->changeQueuePositions($hand, $this->player->hands);
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
     * Calling getQueue method to determine if all players have played, if so the bankPlayer will draw cards and then the result() method is called.
     */
    private function gameEnd(): void
    {
        $queue = $this->queue->getQueue($this->player->hands);
        $boolStrVariables = array_column($queue, 0);
        if (in_array(false, $boolStrVariables) === false) {
            $this->playerDone = true;
        }
        if ($this->playerDone === true) {
            while($this->bankPlayer->hands[0]->points < 17) {
                $this->draw($this->bankPlayer->hands[0], 1);
            }
            $this->bankPlayer->hands[0]->havePlayed = true;
            $this->result();
        }
    }

    /**
     * result function retrieves bankplayer points and compares it to all the hand points to determine if the players balance should be increased depending on the result and bet made.
     */
    private function result(): void
    {
        $bankPoints = $this->bankPlayer->hands[0]->points;
        $amount = count($this->player->hands);
        for ($i = 0; $i < $amount; $i++) {
            $hand = $this->player->hands[$i];
            $handPoints = $hand->points;
            switch (true) {
                case ($handPoints == $bankPoints && $handPoints < 22):
                    $hand->message = "Pushed";
                    $this->player->balance += $hand->currentBet;
                    break;
                case ($handPoints == 21):
                    $hand->message = "You won " . ($hand->currentBet*2.5);
                    $this->player->balance += $hand->currentBet*2.5;
                    break;
                case ($bankPoints <= 21 && $bankPoints >= $handPoints) or ($handPoints > 21):
                    $hand->message = "You lost " . $hand->currentBet;
                    break;
                default:
                    $hand->message = "You won " . $hand->currentBet*2;
                    $this->player->balance += $hand->currentBet*2;
                    break;
            }
        }
    }

    /**
     * @param Hand $hand
     * @param string $choice
     * method makes action for chosen hand.
     */
    public function makeAction(Hand $hand, string $choice): void
    {
        if ($choice == "PullCard") {
            $this->draw($hand, 1);
        }
        if ($choice == "Stay") {
            $this->queue->changeQueuePositions($hand, $this->player->hands);
            $this->gameEnd();
        }
    }

    /**
     * @return int[]
     * method returns array with possible point-outcomes. This method is crucial because Ace can be two different values.
     */
    public function getPointsArray(Hand $hand): array
    {
        $cards = $hand->cards;
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
     * @param Hand $hand
     * method updates the hand-score with the highest possible value.
     */
    public function addPoints(Hand $hand): void
    {
        $points = $this->getPointsArray($hand);
        $hand->points = $points[0];
    }
}
