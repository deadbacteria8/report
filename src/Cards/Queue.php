<?php

namespace App\Cards;

class Queue
{
    /**
     * @param Players[] $playerArray
     */
    public function changeQueuePositions(Players $player, array $playerArray): void
    {
        $amount = count($playerArray);
        $temp = $player->queueSpot;
        $player->havePlayed = true;
        $player->queueSpot = $amount;
        for ($i = 1; $i <= $amount; $i++) {
            if ($playerArray[$i]->queueSpot >= $temp && $playerArray[$i] != $player) {
                $playerArray[$i]->queueSpot -= 1;
            }
        }
    }

    /**
     * @param Players[] $playerArray
     */
    public function getQueue(array $playerArray): array
    {
        $returnArray = [];
        foreach ($playerArray as $player) {
            $returnArray[] = [$player->havePlayed,$player->queueSpot];
        }
        usort($returnArray, function ($first, $second) {
            return $first[1] <=> $second[1];
        });
        return $returnArray;
    }
    /**
     * @param Players[] $playerArray
     * @return Players[] $playerArray
     */
    public function createQueue($playerArray): array
    {
        $amount = count($playerArray);
        for ($i = 1; $i <= $amount; $i++) {
            $player = $playerArray[$i];
            $player->havePlayed = false;
            $player->queueSpot = $i;
        }
        return $playerArray;
    }
}
