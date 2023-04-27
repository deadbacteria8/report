<?php
namespace App\Cards;

use App\Cards\Deck;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DeckTest extends TestCase
{
    public function testPullCardAndLength() : void {
        $deck = new Deck();
        $firstArrayLength = $deck->arrayLength();
        $pulledCards = $deck->pullCard(2);
        $this->assertEquals($firstArrayLength-2, $deck->arrayLength());
        $this->assertEquals(2, count($pulledCards));
    }

    public function testToList() : void {
        $deck = new Deck();
        $toList = $deck->toList($deck->deck);
        foreach($toList as $card) {
            $this->assertIsString($card);
        }
    }

}