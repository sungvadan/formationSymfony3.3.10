<?php

namespace AppBundle\DataCollector;

use AppBundle\Game\GameContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

class GameDataCollector extends DataCollector
{


    /**
     * @var GameContext
     */
    private $context;

    public function __construct(GameContext $context)
    {

        $this->context = $context;
    }


    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['game'] = $this->context->loadGame()?: false;
    }

    public function getGame()
    {
        return $this->data['game'];
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'game';
    }
}