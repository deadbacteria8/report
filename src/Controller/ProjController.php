<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Cards\Card;

use App\Blackjack\BlackJack;

class ProjController extends AbstractController
{
    #[Route("/proj", name: "proj", methods:['GET'])]
    public function projStart(): Response
    {
        return $this->render('proj/proj.html.twig');
    }

    #[Route("/proj/about", name: "projAbout", methods:['GET'])]
    public function projAbout(): Response
    {
        return $this->render('proj/about.html.twig');
    }

    #[Route("/proj", name: "projPost", methods:['POST'])]
    public function projStartPost(SessionInterface $session): Response
    {
        $blackJack = new BlackJack();
        $session->set('projbl67', $blackJack);
        return $this->redirectToRoute('betGet');
    }

    #[Route("/bet", name: "betGet", methods:['GET'])]
    public function betGet(SessionInterface $session): Response
    {
        $blackJack = $session->get('projbl67', "notFound");
        if ($blackJack == "notFound") {
            return $this->redirectToRoute('proj');
        }
        $data = [
            "spelare" => $blackJack->player
        ];
        return $this->render('proj/bet.html.twig', $data);
    }

    #[Route("/bet", name: "betPost", methods:['POST'])]
    public function betPost(SessionInterface $session, Request $request): Response
    {
        $blackJack = $session->get('projbl67');
        $action = $request->request->get('action');
        switch (true) {
            case($action == "Add"):
                $blackJack->player->addHand();
                break;
            case($action == "Bet"):
                $value = $request->request->get('value');
                $hand = $request->request->get('hand');
                $blackJack->bet($blackJack->player->hands[$hand], intVal($value));
                break;
            default:
                $blackJack->start();
                return $this->redirectToRoute('blackJackGet');
        }
        $session->set('projbl67', $blackJack);
        return $this->redirectToRoute('betGet');
    }

    #[Route("/blackjack", name: "blackJackGet", methods:['GET'])]
    public function blackjack(SessionInterface $session): Response
    {
        $blackJack = $session->get('projbl67', "notFound");
        if ($blackJack == "notFound") {
            return $this->redirectToRoute('proj');
        }
        $data = [
            "game" => $blackJack,
        ];
        $session->set('projbl67', $blackJack);
        return $this->render('proj/blackjack.html.twig', $data);
    }

    #[Route("/blackjack", name: "blackJackPost", methods:['POST'])]
    public function blackjackPost(SessionInterface $session, Request $request): Response
    {
        $blackJack = $session->get('projbl67');
        if ($request->request->get('reset')) {
            $blackJack->reset();
            return $this->redirectToRoute('betGet');
        }
        $hand = $request->request->get('count');
        $choice = $request->request->get('value');
        $blackJack->makeAction($blackJack->player->hands[$hand], $choice);
        $session->set('projbl67', $blackJack);
        return $this->redirectToRoute('blackJackGet');
    }
}
