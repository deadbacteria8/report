<?php
namespace App\Cards;

use App\Cards\Game;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class GameTest extends TestCase
{
    public function testCreateObjectWithOnePlayer() : void {
    $game = new Game(1);
    $this->assertInstanceOf("\App\Cards\Game", $game);
    $this->assertInstanceOf("\App\Cards\Players",$game->players[1]);
    }

    public function testStartingGame() : void {
        $game = new Game(5);
        $game->start();
        $playerArray = $game->players;
        $count = count($playerArray);
        for($i = 1; $i <= $count; $i++) {
            $this->assertEquals(2,count($playerArray[$i]->cards));
        }
    }

    public function testBet() : void {
        $game = new Game(1);
        $this->assertEquals(1000, $game->players[1]->balance);
        $game->bet(1,800);
        $this->assertEquals(200, $game->players[1]->balance);
        $this->assertEquals(800, $game->players[1]->currentBet);
    }

    public function testMakeAction() : void {
        $game = new Game(5);
        $game->makeAction(1, "PullCard");
        $this->assertEquals(1, count($game->players[1]->cards));
        $this->assertFalse($game->players[1]->havePlayed);
        $game->makeAction(1, "Stay");
        $this->assertTrue($game->players[1]->havePlayed);
    }

    public function testAllPlayers() : void {
        $game = new Game(1);
        $playerOne = $game->players[1];
        $allPlayersArray = $game->allPlayers();
        $playerOneArray = $allPlayersArray[0];
        $this->assertEquals($playerOne->playerNumber, $playerOneArray["player-number"]);
        $this->assertEquals($playerOne->cards, $playerOneArray["cards"]);
        $this->assertEquals($playerOne->queueSpot, $playerOneArray["Queue-Spot"]);
        $this->assertEquals($game->getPointsArray($playerOne), $playerOneArray["Points-Array"]);
        $this->assertEquals($playerOne->balance, $playerOneArray["Money"]);
        $this->assertEquals($playerOne->message, $playerOneArray["Player-Message"]);
    }

    public function testToList() : void {
        $game = new Game(1);
        $card = new Card([1,11],"♦","A");
        $game->players[1]->cards[] = [$card];
        $toList = $game->toList($game->players[1]->cards);
        $this->assertEquals(["A♦"],$toList);
    }

    public function testGameEnd() : void {
        $game = new Game(1);
        $game->start();
        $game->bankPlayer->points = 16;
        $game->makeAction(1,"Stay");
        $this->assertGreaterThan(2, count($game->bankPlayer->cards));
    }
}