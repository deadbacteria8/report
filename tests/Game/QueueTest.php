<?php
namespace App\Cards;

use App\Cards\Game;
use App\Cards\Queue;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class QueueTest extends TestCase
{
    public function testCreateQueue() : void {
        $game = new Game(5);
        $count = count($game->players);
        $queue = new Queue();
        $queue->createQueue($game->players);
        for($i = 0; $i < $count; $i++) {
            $this->assertEquals($game->players[$i]->queueSpot, $i+1);
        }
    }

    public function testQueueChange() : void {
        $game = new Game(5);
        $count = count($game->players);
        $queue = new Queue();
        $queue->changeQueuePositions($game->players[0],$game->players);
        $this->assertTrue($game->players[0]->havePlayed);
        $this->assertEquals($count,$game->players[0]->queueSpot);
    }

    public function testGetArray() : void {
        $game = new Game(5);
        $queue = new Queue();
        $queue->createQueue($game->players);
        $this->assertIsArray($queue->getQueue($game->players));
    }
}