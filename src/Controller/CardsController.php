<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Cards\Deck;

class CardsController extends AbstractController
{
    #[Route("/card", name: "card")]
    public function card(): Response
    {
        return $this->render('card.html.twig');
    }

    #[Route("/card/deck", name: "deck")]
    public function deck(SessionInterface $session): Response
    {
        $deckClass = $session->get('deck', new Deck());
        $deck = $deckClass->getDeck();
        $data = [
            "list1" => $deckClass->toList($deck),
        ];
        return $this->render('deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "shuffle")]
    public function deckShuffle(SessionInterface $session): Response
    {
        $deckClass = new Deck();
        $session->set('deck', $deckClass);
        $deck = $deckClass->getDeck();
        $toList = $deckClass->toList($deck);
        shuffle($toList);
        $data = [
            "list1" => $toList,
        ];
        return $this->render('shuffle.html.twig', $data);
    }

        #[Route("/card/deck/draw/{num<\d+>}", name: "draw2")]
        public function draw2(int $num, SessionInterface $session): Response
        {
            $deckClass = $session->get('deck', new Deck());
            $toList = [];
            $count = $deckClass->arraylength();
            if ($count >= $num) {
                $cards = $deckClass->pullCard($num);
                $toList = $deckClass->toList($cards);
            }
            $count = $deckClass->arraylength();
            $session->set('deck', $deckClass);
            $data = [
                "list1" => $toList,
                "count" => $count,
            ];
            return $this->render('draw.html.twig', $data);
        }

        #[Route("/card/deck/draw", name: "draw", methods: ['GET'])]
        public function draw(SessionInterface $session): Response
        {
            $deckClass = $session->get('deck', new Deck());
            $toList = [];
            $count = $deckClass->arraylength();
            if ($count > 0) {
                $cards = $deckClass->pullCard(1);
                $toList = $deckClass->toList($cards);
            }
            $count = $deckClass->arraylength();
            $session->set('deck', $deckClass);
            $data = [
                "list1" => $toList,
                "count" => $count,
            ];
            return $this->render('draw.html.twig', $data);
        }

        #[Route("/card/deck/draw", name: "draw_post", methods: ['POST'])]
        public function initCallback(Request $request): Response
        {
            $num = $request->request->get('value');

            return $this->redirectToRoute('draw2', ['num' => $num]);
        }
}
