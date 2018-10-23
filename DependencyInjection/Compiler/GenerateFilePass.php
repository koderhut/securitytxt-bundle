<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\DependencyInjection\Compiler;

use KoderHut\SecurityTxt\SecurityTxt;
use KoderHut\SecurityTxtBundle\Exception\PathNotFoundException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class GenerateFilePass
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 */
class GenerateFilePass implements CompilerPassInterface
{
    const OUTPUT_TO_FILE = 'file';

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        if (
            false === $container->hasParameter('security_txt.enabled')
            || false === $container->getParameter('security_txt.enabled')
            || self::OUTPUT_TO_FILE !== $container->getParameter('security_txt.output_to')
        ) {
            return;
        }

        $documentPath = $container->getParameter('security_txt.output_path');
        /** @var Filesystem $filesystem */
        $filesystem = $container->get('filesystem');

        if (!$filesystem->exists($documentPath)) {
            if (!$container->getParameter('security_txt.create_path')) {
                throw new PathNotFoundException(
                    sprintf('Folder path [%s] is not accessible and we cannot create the path due to configuration.', $documentPath)
                );
            }

            $filesystem->mkdir($documentPath, 0755);
        }

        /** @var SecurityTxt $securitytxt */
        $securitytxt = $container->get(SecurityTxt::class);
        $filesystem->dumpFile($documentPath . '/security.txt', $securitytxt->__toString());
    }
}
