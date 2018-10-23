<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle;

use KoderHut\SecurityTxtBundle\DependencyInjection\Compiler\GenerateFilePass;
use KoderHut\SecurityTxtBundle\DependencyInjection\Compiler\SecurityTxtDocumentBuildPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SecurityTxtBundle
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 */
class SecurityTxtBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SecurityTxtDocumentBuildPass());
        $container->addCompilerPass(new GenerateFilePass());
    }

}