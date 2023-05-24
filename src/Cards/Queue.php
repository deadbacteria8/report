<?php

namespace App\Cards;

use App\Blackjack\Hand;

class Queue
{
    /**
     * @param Players|Hand $player
     * @param Players[]|Hand[] $playerArray
     * construct method
     */
    public function changeQueuePositions(Players|Hand $player, array $playerArray): void
    {
        $amount = count($playerArray);
        $temp = $player->queueSpot;
        $player->havePlayed = true;
        $player->queueSpot = $amount;
        for ($i = 0; $i < $amount; $i++) {
            if ($playerArray[$i]->queueSpot >= $temp && $playerArray[$i] != $player) {
                $playerArray[$i]->queueSpot -= 1;
            }
        }
    }

    /**
     * @param Players[]|Hand[] $playerArray
     */
    public function getQueue(array $playerArray): array
    {
        $returnArray = [];
        foreach ($playerArray as $player) {
            $returnArray[] = [$player->havePlayed,$player->queueSpot];
        }
        return $returnArray;
    }

    /**
     * @param Players[]|Hand[] $playerArray
     * @return Players[]|Hand[] $playerArray
     */
    public function createQueue(array $playerArray): array
    {
        $amount = count($playerArray);
        for ($i = 0; $i < $amount; $i++) {
            $player = $playerArray[$i];
            $player->havePlayed = false;
            $player->queueSpot = $i+1;
        }
        return $playerArray;
    }
}
