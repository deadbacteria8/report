<?php
namespace App\Blackjack;

use PHPUnit\Framework\TestCase;

use App\Cards\Card;
class BlackJackTest extends TestCase
{
    public function testStartingGame() : void {
        $blackJack = new BlackJack();
        $blackJack->player->addHand();
        $blackJack->start();
        $hands = $blackJack->player->hands;
        $count = count($hands);
        for($i = 0; $i < $count; $i++) {
            $this->assertEquals(2,count($hands[$i]->cards));
        }
    }

    public function testBet() : void {
        $blackJack = new BlackJack();
        $blackJack->player->addHand();
        $this->assertEquals(1000, $blackJack->player->balance);
        $blackJack->bet($blackJack->player->hands[0],800);
        $this->assertEquals(200, $blackJack->player->balance);
        $this->assertEquals(800, $blackJack->player->hands[0]->currentBet);
        $this->assertEquals(0, $blackJack->player->hands[1]->currentBet);
    }

    public function testMakeAction() : void {
        $blackJack = new BlackJack();
        $blackJack->makeAction($blackJack->player->hands[0], "PullCard");
        $this->assertEquals(1, count($blackJack->player->hands[0]->cards));
        $this->assertFalse($blackJack->player->hands[0]->havePlayed);
        $blackJack->makeAction($blackJack->player->hands[0], "Stay");
        $this->assertTrue($blackJack->player->hands[0]->havePlayed);
    }

    public function testGameEnd() : void {
        $blackJack = new BlackJack();
        $blackJack->start();
        $blackJack->bankPlayer->hands[0]->points = 16;
        $blackJack->makeAction($blackJack->player->hands[0], "Stay");
        $this->assertGreaterThan(2, count($blackJack->bankPlayer->hands[0]->cards));
    }

    public function testHand21() : void {
        $blackJack = new BlackJack();
        $blackJack->player->addHand();
        $blackJack->bet($blackJack->player->hands[0],1000);
        $blackJack->bet($blackJack->player->hands[1],100);
        $blackJack->bankPlayer->hands[0]->points = 17;
        $blackJack->player->hands[0]->points = 21;
        $blackJack->player->hands[1]->points = 16;
        $blackJack->makeAction($blackJack->player->hands[0], "Stay");
        $blackJack->makeAction($blackJack->player->hands[1], "Stay");
        $this->assertEquals($blackJack->player->balance,1000*2.5 - 100);
    }

    public function testPlayerNormalWin() : void {
        $blackJack = new BlackJack();
        $blackJack->bet($blackJack->player->hands[0],1000);
        $blackJack->bankPlayer->hands[0]->points = 17;
        $blackJack->player->hands[0]->points = 20;
        $blackJack->makeAction($blackJack->player->hands[0], "Stay");
        $this->assertEquals($blackJack->player->balance,1000*2);
    }


    public function testPlayerTie() : void {
        $blackJack = new BlackJack();
        $blackJack->bet($blackJack->player->hands[0],1000);
        $blackJack->bankPlayer->hands[0]->points = 20;
        $blackJack->player->hands[0]->points = 20;
        $blackJack->makeAction($blackJack->player->hands[0], "Stay");
        $this->assertEquals($blackJack->player->balance,1000);
    }

    public function testPlayerLose() : void {
        $blackJack = new BlackJack();
        $blackJack->bet($blackJack->player->hands[0],1000);
        $blackJack->bankPlayer->hands[0]->points = 20;
        $blackJack->player->hands[0]->points = 19;
        $blackJack->makeAction($blackJack->player->hands[0], "Stay");
        print_r($blackJack->player->balance);
        $this->assertEquals($blackJack->player->balance,0);
    }

    public function testReset() : void {
        $blackJack = new BlackJack();
        $blackJack->player->hands[0]->points = 21;
        $blackJack->reset();
        $this->assertEquals($blackJack->player->hands[0]->points,0);
    }
}