<?php
namespace App\Blackjack;

use App\Blackjack\Player;
use PHPUnit\Framework\TestCase;

class PlayerAndHandTest extends TestCase
{
    public function testConstruct() : void {
        $blackJack = new BlackJack();
        $player = $blackJack->player;
        $this->assertEquals(count($player->hands), 1);
        $player->addHand();
        $this->assertEquals(count($player->hands), 2);
        $this->assertEquals($player->balance, 1000);
    }
    public function testNotSame() : void {
        $blackJack = new BlackJack();
        $player = $blackJack->player;
        $bank = $blackJack->bankPlayer;
        $this->assertNotSame($player, $bank);
        $this->assertSame($player->hands[0]->owner, $player);
    }
}