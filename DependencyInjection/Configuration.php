<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $root = $tree->root('security_txt');

        $root
            ->canBeEnabled()
            ->treatNullLike(array('enabled' => false))
            ->children()
                ->enumNode('output_to')
                    ->values(['file'])
                    ->defaultValue('file')
                    ->treatNullLike('file')
                ->end()
                ->scalarNode('path')
                    ->defaultValue('/public')
                    ->info('Set the public folder path relative to the project root folder')
                    ->example('For Symfony 4.0 and above use "/public" and for Symfony versions below 4.0 use /web')
                ->end()
                ->booleanNode('create_path')
                    ->defaultTrue()
                    ->info('Create the folder path if it does not exits')
                ->end()
                ->append($this->addInformationConfig())
            ->end()
        ;

        return $tree;
    }

    /**
     * Add the information config
     *
     * @return ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function addInformationConfig()
    {
        $tree = new TreeBuilder();
        $root = $tree->root('info');
        $root
            ->isRequired()
            ->children()
                ->arrayNode('contact')
                    ->isRequired()
                    ->children()
                        ->scalarNode('comment')->defaultValue('For security issues please contact us using one of the methods listed below')->end()
                        ->scalarNode('email')->end()
                        ->scalarNode('phone')->end()
                        ->scalarNode('url')->end()
                    ->end()
                ->end()

                ->arrayNode('encryption')
                    ->children()
                        ->scalarNode('comment')->defaultValue('Our encryption key')->end()
                        ->scalarNode('url')->defaultValue('')->end()
                    ->end()
                ->end()

                ->arrayNode('acknowledgements')
                    ->children()
                        ->scalarNode('comment')->defaultValue('Out hall-of-fame')->end()
                        ->scalarNode('url')->defaultValue('')->end()
                    ->end()
                ->end()

                ->arrayNode('policy')
                    ->children()
                        ->scalarNode('comment')->defaultValue('Our policy on security issues disclosure')->end()
                        ->scalarNode('url')->defaultValue('')->end()
                    ->end()
                ->end()

                ->arrayNode('signature')
                    ->children()
                        ->scalarNode('comment')->defaultValue('Our signature file')->end()
                        ->scalarNode('url')->defaultValue('')->end()
                    ->end()
                ->end()

                ->arrayNode('hiring')
                    ->children()
                        ->scalarNode('comment')->defaultValue('Checkout our open job positions')->end()
                        ->scalarNode('url')->defaultValue('')->end()
                    ->end()
                ->end()

            ->end() // closing the children tags
        ->end(); // closing the root element

        return $root;
    }
}
