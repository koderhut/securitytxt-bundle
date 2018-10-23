<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\Tests\DependencyInjection;

use KoderHut\SecurityTxtBundle\DependencyInjection\SecurityTxtExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SecurityTxtExtensionTest
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 *
 * @covers \KoderHut\SecurityTxtBundle\DependencyInjection\SecurityTxtExtension
 */
class SecurityTxtExtensionTest extends TestCase
{

    /**
     * @test
     */
    public function testThrowExceptionUnableToLoadConfigurationClass()
    {
        $instance = new class () extends SecurityTxtExtension {
            public function getConfiguration(array $config, ContainerBuilder $container)
            {
                return null;
            }
        };

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Unable to locate configuration class');

        $instance->load([], new ContainerBuilder());
    }

    /**
     * @test
     */
    public function testContainerIsNotChangedIfExtensionIsDisabled()
    {
        $instance  = new SecurityTxtExtension();
        $container = new ContainerBuilder();
        $configs   = ['security_txt' => ['enabled' => false, 'info' => ['contact' => []]]];

        $instance->load($configs, $container);

        $this->assertFalse($container->hasParameter('security_txt.enabled'));
        $this->assertFalse($container->hasParameter('security_txt.info'));
        $this->assertFalse($container->hasParameter('security_txt.output_to'));
        $this->assertFalse($container->hasParameter('security_txt.output_path'));
        $this->assertFalse($container->hasParameter('security_txt.create_path'));
    }

    /**
     * @test
     */
    public function testContainerContainsAllOptionsNeeded()
    {
        $instance  = new SecurityTxtExtension();
        $container = new ContainerBuilder();
        $configs   = ['security_txt' => ['enabled' => true, 'info' => ['contact' => []]]];

        $container->setParameter('kernel.root_dir', '/tmp/');

        $instance->load($configs, $container);

        $this->assertTrue($container->hasParameter('security_txt.enabled'));
        $this->assertTrue($container->hasParameter('security_txt.info'));
        $this->assertTrue($container->hasParameter('security_txt.output_to'));
        $this->assertTrue($container->hasParameter('security_txt.output_path'));
        $this->assertTrue($container->hasParameter('security_txt.create_path'));
    }
}
