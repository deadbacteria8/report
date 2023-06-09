<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Cards\Game;
use App\Cards\Deck;
use App\Entity\Book;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\BookRepository;

class Api extends AbstractController
{
    #[Route("/api/qoute", name:"apiqoute")]
    public function jsonNumber(): Response
    {
        $quotes = [
            'The greatest glory in living lies not in never falling, but in rising every time we fall. -Nelson Mandela',
            'The way to get started is to quit talking and begin doing. -Walt Disney',
            'The future belongs to those who believe in the beauty of their dreams. -Eleanor Roosevelt'
        ];

        $randomIndex = rand(0, 2);
        $randomQuote = $quotes[$randomIndex];

        date_default_timezone_set('Europe/Stockholm');
        $currentTime = time();
        $formattedTime = date('h:i:s A', $currentTime);
        $data = [
            'quote' => $randomQuote,
            'datum' => date('Y-m-d'),
            'tid' => $formattedTime
        ];

        return new JsonResponse($data);
    }

    #[Route("/api/deck", name:"apideck")]
    public function apideck(SessionInterface $session): Response
    {
        $deckClass = $session->get('deck', new Deck());
        $deck = $deckClass->getDeck();
        $data = [
            "deck" => $deckClass->toList($deck),
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/deck/shuffle", name:"api_deck_post", methods: ['POST'])]
    public function apiShuffle(SessionInterface $session): Response
    {
        $deckClass = new Deck();
        $session->set('deck', $deckClass);
        $deck = $deckClass->getDeck();
        $toList = $deckClass->toList($deck);
        shuffle($toList);
        $data = [
            "shuffled" => $toList,
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/deck/shuffle", name:"apideck_get", methods: ['GET'])]
    public function apiShuffleGet(): Response
    {
        return $this->render('apishuffle.html.twig');
    }

    #[Route("/api", name:"api")]
    public function api(): Response
    {
        return $this->render('api.html.twig');
    }

    #[Route("/api/deck/draw", name: "apidraw", methods: ['GET'])]
    public function apidraw(): Response
    {
        return $this->render('apidraw.html.twig');
    }

    #[Route("/api/deck/draw/{num<\d+>}", name: "apidraw2")]
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
            "drawed" => $toList,
            "count" => $count,
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/game", name: "apigame")]
    public function game(SessionInterface $session): Response
    {
        $game = $session->get('bl', new Game(1));
        $data = [
            "GameCurrentlyPlaying" => $game->gamePlaying,
            "BankCards" => $game->toList($game->bankPlayer->cards),
            "Players" => $game->allPlayers()
        ];
        return new JsonResponse($data);
    }

    #[Route("/api/deck/draw", name: "apidraw_post", methods: ['POST'])]
    public function initCallback2(Request $request, SessionInterface $session): Response
    {
        $num = $request->request->get('value');
        if (empty($num)) {
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
                "drawed_card" => $toList,
                "count" => $count,
            ];
            return new JsonResponse($data);
        }
        return $this->redirectToRoute('apidraw2', ['num' => $num]);
    }

    #[Route('api/library/book/{isbn}', name: 'bookByIsbn')]
    public function showBookById(string $isbn, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $book = $entityManager->getRepository(Book::class)->findOneBy(['ISBN' => $isbn]);
        if($book) {
            return $this->json($book);
        }
        return $this->json([
            'error' => 'Book not found'
        ], 404);
    }

    #[Route('api/library/books', name: 'booksApi')]
    public function showBooks(BookRepository $booksRepository): Response
    {
        return $this->json($booksRepository->findAll());
    }
}
