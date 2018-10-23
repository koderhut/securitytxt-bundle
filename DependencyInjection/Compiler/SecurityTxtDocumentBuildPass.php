<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\DependencyInjection\Compiler;

use KoderHut\SecurityTxt\SecurityTxt;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class SecurityTxtDocumentBuildPass
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 */
class SecurityTxtDocumentBuildPass implements CompilerPassInterface
{

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasParameter('security_txt.enabled') || false === $container->hasParameter('security_txt.info')) {
            return;
        }

        $document = $container->getDefinition(SecurityTxt::class);
        $config   = $container->getParameter('security_txt.info');

        foreach ($config as $directiveName => $directiveConfig) {
            $directiveReference = $this->buildDirectiveDefinition($directiveName, $directiveConfig);

            $document->addMethodCall('addDirective', [$directiveReference]);
        }
    }

    /**
     * Build an definition for a directive
     *
     * @param string $directiveName
     * @param array $directiveConfig
     *
     * @return Definition
     */
    private function buildDirectiveDefinition(string $directiveName, array $directiveConfig): Definition
    {
        $directiveNs = 'KoderHut\\SecurityTxt\\Directive\\';
        $lineNs      = 'KoderHut\\SecurityTxt\\Line\\';
        $className   = $directiveNs . ucfirst($directiveName);

        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf('Unknown directive [%s]', $directiveName));
        }

        $directive = new Definition($className);

        $commentDefinition = new Definition($lineNs.'Comment');
        $commentDefinition->addArgument($directiveConfig['comment']);

        $directive->addMethodCall('addCommentLine', [$commentDefinition]);
        unset($directiveConfig['comment']);

        foreach ($directiveConfig as $lineId => $lineConfig) {
            $line = new Definition($lineNs . ucfirst($lineId), [$lineConfig]);
            $directive->addArgument($line);
        }

        return $directive;
    }
}
