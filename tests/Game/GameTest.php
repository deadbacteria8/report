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
    $this->assertInstanceOf("\App\Cards\Players",$game->players[0]);
    }

    public function testStartingGame() : void {
        $game = new Game(5);
        $game->start();
        $playerArray = $game->players;
        $count = count($playerArray);
        for($i = 0; $i < $count; $i++) {
            $this->assertEquals(2,count($playerArray[$i]->cards));
        }
    }

    public function testBet() : void {
        $game = new Game(1);
        $this->assertEquals(1000, $game->players[0]->balance);
        $game->bet(0,800);
        $this->assertEquals(200, $game->players[0]->balance);
        $this->assertEquals(800, $game->players[0]->currentBet);
    }

    public function testMakeAction() : void {
        $game = new Game(5);
        $game->makeAction(0, "PullCard");
        $this->assertEquals(1, count($game->players[0]->cards));
        $this->assertFalse($game->players[0]->havePlayed);
        $game->makeAction(0, "Stay");
        $this->assertTrue($game->players[0]->havePlayed);
    }

    public function testAllPlayers() : void {
        $game = new Game(1);
        $playerOne = $game->players[0];
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
        $game->players[0]->cards[] = [$card];
        $toList = $game->toList($game->players[0]->cards);
        $this->assertEquals(["A♦"],$toList);
    }

    public function testGameEnd() : void {
        $game = new Game(1);
        $game->start();
        $game->bankPlayer->points = 16;
        $game->makeAction(0,"Stay");
        $this->assertGreaterThan(2, count($game->bankPlayer->cards));
    }

    public function testPlayer21() : void {
        $game = new Game(1);
        $game->start();
        $game->bet(0,1000);
        $game->bankPlayer->points = 17;
        $game->players[0]->points = 21;
        $game->makeAction(0, "Stay");
        $this->assertEquals($game->players[0]->balance,1000*2.5);
    }

    public function testPlayerNormalWin() : void {
        $game = new Game(1);
        $game->start();
        $game->bet(0,1000);
        $game->bankPlayer->points = 17;
        $game->players[0]->points = 20;
        $game->makeAction(0, "Stay");
        $this->assertEquals($game->players[0]->balance,1000*2);
    }

    public function testPlayerTie() : void {
        $game = new Game(1);
        $game->start();
        $game->bet(0,1000);
        $game->bankPlayer->points = 20;
        $game->players[0]->points = 20;
        $game->makeAction(0, "Stay");
        $this->assertEquals($game->players[0]->balance,1000);
    }

    public function testPlayerLose() : void {
        $game = new Game(1);
        $game->start();
        $game->bet(0,1000);
        $game->bankPlayer->points = 21;
        $game->players[0]->points = 20;
        $game->makeAction(0, "Stay");
        $this->assertEquals($game->players[0]->balance,0);
    }
}