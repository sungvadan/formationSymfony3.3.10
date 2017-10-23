<?php
/**
 * Created by PhpStorm.
 * User: ogcinformatique
 * Date: 23/10/2017
 * Time: 17:06
 */

namespace AppBundle\Compliler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GamePass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
    }
}