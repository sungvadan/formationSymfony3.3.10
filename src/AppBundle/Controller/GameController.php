<?php

namespace AppBundle\Controller;

use AppBundle\Game\GameContext;
use AppBundle\Game\GameRunner;
use AppBundle\Game\Loader\TextFileLoader;
use AppBundle\Game\Loader\XmlFileLoader;
use AppBundle\Game\WordList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * This action displays the main board to play the game.
     *
     * @Route("", name="app_game_play")
     * @Method("GET")
     */
    public function playAction()
    {
        $game = $this->createGameRunner(true)->loadGame();

        return $this->render('game/board.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * This action displays the congratulations page.
     *
     * @Route("/win", name="app_game_win")
     * @Method("GET")
     */
    public function winAction()
    {
        try {
            $game = $this->createGameRunner()->resetGameOnSuccess();
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_game_play');
        }

        return $this->render('game/win.html.twig', ['game' => $game]);
    }

    /**
     * This action displays the losing page.
     *
     * @Route("/fail", name="app_game_fail")
     * @Method("GET")
     */
    public function failAction()
    {
        try {
            $game = $this->createGameRunner()->resetGameOnFailure();
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_game_play');
        }

        return $this->render('game/fail.html.twig', ['game' => $game]);
    }

    /**
     * This action resets the current game and starts a new one.
     *
     * @Route("/reset", name="app_game_reset")
     * @Method("GET")
     */
    public function resetAction()
    {
        $this->createGameRunner()->resetGame();

        return $this->redirectToRoute('app_game_play');
    }

    /**
     * This action tries one single letter at a time.
     *
     * @Route("/play/{letter}", name="app_game_play_letter", requirements={"letter"="[a-z]"})
     * @Method("GET")
     */
    public function playLetterAction($letter)
    {
        $game = $this->createGameRunner()->playLetter($letter);

        if (!$game->isOver()) {
            return $this->redirectToRoute('app_game_play');
        }

        return $this->redirectToRoute($game->isWon() ? 'app_game_win' : 'app_game_fail');
    }

    /**
     * This action tries one single word at a time.
     *
     * @Route(
     *   path="/play",
     *   name="app_game_play_word",
     *   condition="request.request.get('word') matches '/^[a-z]+$/i'"
     * )
     * @Method("POST")
     */
    public function playWordAction(Request $request)
    {
        $game = $this->createGameRunner()->playWord($request->request->get('word'));

        return $this->redirectToRoute($game->isWon() ? 'app_game_win' : 'app_game_fail');
    }

    private function createGameRunner($withWordList = false)
    {
        return $this->get('hangman');
    }


}
