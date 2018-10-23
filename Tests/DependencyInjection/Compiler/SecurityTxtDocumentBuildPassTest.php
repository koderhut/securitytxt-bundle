<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\Tests\DependencyInjection\Compiler;

use KoderHut\SecurityTxt\SecurityTxt;
use KoderHut\SecurityTxtBundle\DependencyInjection\Compiler\SecurityTxtDocumentBuildPass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class SecurityTxtDocumentBuildPassTest
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 *
 * @covers \KoderHut\SecurityTxtBundle\DependencyInjection\Compiler\SecurityTxtDocumentBuildPass
 */
class SecurityTxtDocumentBuildPassTest extends TestCase
{

    /**
     * @test
     */
    public function testExitEarlyIfInfoIsMissingOrBundleIsDisabled()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $instance  = new SecurityTxtDocumentBuildPass();

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(false);

        $instance->process($container->reveal());

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->hasParameter('security_txt.info')->shouldBeCalled()->willReturn(false);

        $instance->process($container->reveal());
    }

    /**
     * @test
     */
    public function testModifyDocumentDefinition()
    {
        $container          = $this->prophesize(ContainerBuilder::class);
        $documentDefinition = $this->prophesize(Definition::class);
        $instance           = new SecurityTxtDocumentBuildPass();
        $config             = [
            'contact' => [
                'comment' => 'test comment',
                'email'   => 'test@email.com',
            ],
        ];

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->hasParameter('security_txt.info')->shouldBeCalled()->willReturn(true);
        $container->getDefinition(SecurityTxt::class)->shouldBeCalled()->willReturn($documentDefinition->reveal());
        $container->getParameter('security_txt.info')->shouldBeCalled()->willReturn($config);
        $documentDefinition->addMethodCall('addDirective', Argument::containing(Argument::type(Definition::class)))->shouldBeCalled();

        $instance->process($container->reveal());
    }

    /**
     * @test
     */
    public function testThrowExceptionWhenDirectiveClassDoesNotExist()
    {
        $container          = $this->prophesize(ContainerBuilder::class);
        $documentDefinition = $this->prophesize(Definition::class);
        $instance           = new SecurityTxtDocumentBuildPass();
        $config             = ['test' => []];

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->hasParameter('security_txt.info')->shouldBeCalled()->willReturn(true);
        $container->getDefinition(SecurityTxt::class)->shouldBeCalled()->willReturn($documentDefinition->reveal());
        $container->getParameter('security_txt.info')->shouldBeCalled()->willReturn($config);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown directive [test]');

        $instance->process($container->reveal());
    }
}
