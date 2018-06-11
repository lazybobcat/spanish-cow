<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loicb
 * Date: 08/06/18
 * Time: 13:44
 */

namespace Nvision\SpanishCowAdapter\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('nvision_spanish_cow_adapter');

        /*
         * nvision_spanish_cow_adapter:
         *     base_url: https://whatever.com
         *     username: XXXXX
         *     password: WWWWW
         *     project: Z
         *     domains:
         *         - messages
         */
        $rootNode
            ->children()
                ->scalarNode('base_url')->end()
                ->scalarNode('username')->end()
                ->scalarNode('password')->end()
                ->scalarNode('project')->end()
                ->arrayNode('domains')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
