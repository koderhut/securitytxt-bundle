<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Class SecurityTxtExtension
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 */
class SecurityTxtExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));

        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);

        if (null === $configuration) {
            throw new \UnexpectedValueException('Unable to locate configuration class');
        }

        $config = $this->processConfiguration($configuration, $configs);

        if (true !== $config['enabled']) {
            return;
        }

        $container->setParameter('security_txt.enabled', $config['enabled']);
        $container->setParameter('security_txt.info', $config['info']);
        $container->setParameter('security_txt.output_to', $config['output_to']);

        $outputPath = $container->getParameter('kernel.root_dir') . '/../' . trim($config['path'], '/') . '/.well-known';
        $container->setParameter('security_txt.output_path', $outputPath);
        $container->setParameter('security_txt.create_path', $config['create_path']);
    }
}
