<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\Tests\DependencyInjection\Compiler;

use KoderHut\SecurityTxt\SecurityTxt;
use KoderHut\SecurityTxtBundle\DependencyInjection\Compiler\GenerateFilePass;
use KoderHut\SecurityTxtBundle\Exception\PathNotFoundException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class GenerateFilePassTest
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 *
 * @covers \KoderHut\SecurityTxtBundle\DependencyInjection\Compiler\GenerateFilePass
 */
class GenerateFilePassTest extends TestCase
{
    /**
     * @test
     */
    public function testExitEarlyWhenBundleIsDisabledOrOutputIsNotFile()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $instance  = new GenerateFilePass();

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(false);
        $instance->process($container->reveal());

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->getParameter('security_txt.enabled')->shouldBeCalled()->willReturn(false);

        $instance->process($container->reveal());

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->getParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->getParameter('security_txt.output_to')->shouldBeCalled()->willReturn('other');

        $instance->process($container->reveal());
    }

    /**
     * @test
     */
    public function testGenerateFolderStructureIfNotExists()
    {
        $container   = $this->prophesize(ContainerBuilder::class);
        $filesystem  = $this->prophesize(Filesystem::class);
        $securitytxt = $this->prophesize(SecurityTxt::class);
        $instance    = new GenerateFilePass();

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->getParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->getParameter('security_txt.output_to')->shouldBeCalled()->willReturn('file');
        $container->getParameter('security_txt.output_path')->shouldBeCalled()->willReturn('/tmp');
        $container->get('filesystem')->shouldBeCalled()->willReturn($filesystem->reveal());
        $filesystem->exists('/tmp')->shouldBeCalled()->willReturn(false);
        $container->getParameter('security_txt.create_path')->shouldBeCalled()->willReturn(true);
        $filesystem->mkdir('/tmp', 0755)->shouldBeCalled()->willReturn(null);
        $container->get(SecurityTxt::class)->shouldBeCalled()->willReturn($securitytxt->reveal());
        $securitytxt->__toString()->shouldBeCalled()->willReturn('test');
        $filesystem->dumpFile('/tmp/security.txt', 'test')->shouldBeCalled()->willReturn(null);

        $instance->process($container->reveal());
    }

    /**
     * @test
     */
    public function testThrowAnExceptionIfPathDoesNotExistAndConfigDoesNotAllowToCreateIt()
    {
        $container   = $this->prophesize(ContainerBuilder::class);
        $filesystem  = $this->prophesize(Filesystem::class);
        $instance    = new GenerateFilePass();

        $container->hasParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->getParameter('security_txt.enabled')->shouldBeCalled()->willReturn(true);
        $container->getParameter('security_txt.output_to')->shouldBeCalled()->willReturn('file');
        $container->getParameter('security_txt.output_path')->shouldBeCalled()->willReturn('/tmp');
        $container->get('filesystem')->shouldBeCalled()->willReturn($filesystem->reveal());
        $filesystem->exists('/tmp')->shouldBeCalled()->willReturn(false);
        $container->getParameter('security_txt.create_path')->shouldBeCalled()->willReturn(false);

        $this->expectException(PathNotFoundException::class);
        $this->expectExceptionMessage('Folder path [/tmp] is not accessible and we cannot create the path due to configuration.');

        $instance->process($container->reveal());
    }
}
