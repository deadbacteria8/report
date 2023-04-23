<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Cards\Game;

class GameController extends AbstractController
{
    #[Route("/game/start", name: "start", methods:['GET'])]
    public function start(): Response
    {
        return $this->render('start.html.twig');
    }

    #[Route("/game", name: "game")]
    public function card(): Response
    {
        return $this->render('gameFirstPage.html.twig');
    }

    #[Route("/game/doc", name: "doc")]
    public function doc(): Response
    {
        return $this->render('doc.html.twig');
    }

    #[Route("/game/start", name: "start_post", methods:['POST'])]
    public function startPost(Request $request, SessionInterface $session): Response
    {
        $value = $request->request->get('value');
        $gameClass = new Game(intval($value));
        $session->clear();
        $session->set('bl', $gameClass);
        return $this->redirectToRoute('playingfield_get');
    }

    #[Route("/game/playingfield", name: "playingfield_get", methods:['GET'])]
    public function playingFieldGet(SessionInterface $session): Response
    {
        $gameClass = $session->get('bl', "notFound");
        if ($gameClass == "notFound") {
            return $this->redirectToRoute('start');
        }
        $data = [
            "spelare" => $gameClass->players,
            "pengar" => $gameClass->getMoney()
        ];
        return $this->render('playingfield.html.twig', $data);
    }

    #[Route("/game/playingfield", name: "playingfield_post", methods:['POST'])]
    public function playingFieldPost(Request $request, SessionInterface $session): Response
    {
        $gameClass = $session->get('bl');
        $action = $request->request->get('action');
        if ($action == "Start") {
            $gameClass->start();
            return $this->redirectToRoute('game_get');
        }
        $value = $request->request->get('value');
        $player = $request->request->get('player');
        $gameClass->bet(intval($player), intval($value));
        return $this->redirectToRoute('playingfield_get');
    }

    #[Route("/game/game", name: "game_get", methods:['GET'])]
    public function game(SessionInterface $session): Response
    {
        $gameClass = $session->get('bl', "notFound");
        if ($gameClass == "notFound") {
            return $this->redirectToRoute('start');
        }
        $bankPlayer = $gameClass->bankPlayer;
        $data = [
            "bank_cards" => $gameClass->toList($bankPlayer->cards),
            "spelare" => $gameClass->allPlayers(),
            "playersDone" => $gameClass->playersDone,
            "gamePlaying" =>$gameClass->gamePlaying
        ];
        $session->set('bl', $gameClass);
        return $this->render('game.html.twig', $data);
    }

    #[Route("/game/game", name: "gamePost", methods:['POST'])]
    public function gamePost(SessionInterface $session, Request $request): Response
    {
        $player = $request->request->get('count');
        $choice = $request->request->get('value');
        $gameClass = $session->get('bl');
        $gameClass->makeAction(intval($player), $choice);
        $session->set('bl', $gameClass);
        return $this->redirectToRoute('game_get');
    }
}
