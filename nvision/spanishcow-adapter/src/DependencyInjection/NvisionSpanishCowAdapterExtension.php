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
 * Time: 13:42
 */

namespace Nvision\SpanishCowAdapter\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class NvisionSpanishCowAdapterExtension extends Extension
{
    /**
     * Loads a specific configuration.
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('Nvision\SpanishCowAdapter\Client');
        $definition->replaceArgument('$baseUrl', $config['base_url']);
        $definition->replaceArgument('$username', $config['username']);
        $definition->replaceArgument('$password', $config['password']);
        $definition->replaceArgument('$project', $config['project']);
        $definition->addMethodCall('setLogger', [new Reference('logger')]);

        $definition = $container->getDefinition('Nvision\SpanishCowAdapter\SpanishCow');
        $definition->replaceArgument('$client', new Reference('Nvision\SpanishCowAdapter\Client'));
        $definition->replaceArgument('$domains', $config['domains']);
    }
}
