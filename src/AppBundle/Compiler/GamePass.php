<?php
/**
 * Created by PhpStorm.
 * User: ogcinformatique
 * Date: 23/10/2017
 * Time: 17:06
 */

namespace AppBundle\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GamePass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if(!$container->hasDefinition('game.word_list')){
            return;
        }

        $wordList = $container->findDefinition('game.word_list');

        // Delete the configuration
        $wordList->setMethodCalls([]);

        $wordList->addMethodCall('addWord',['shaker']);

        $loaders = $container->findTaggedServiceIds('game.loader');
        foreach ($loaders as $id => $parameters){
            $wordList->addMethodCall('addLoader',[$parameters[0]['type'], new Reference($id)]);
        }

        $wordList->addMethodCall('loadDictionaries',['%dictionaries%']);
    }
}