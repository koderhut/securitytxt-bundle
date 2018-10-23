<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\Tests\DependencyInjection;

use KoderHut\SecurityTxtBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 *
 * @covers \KoderHut\SecurityTxtBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends TestCase
{

    /**
     * @test
     *
     * @param array $options
     * @param array $expected
     *
     * @dataProvider optionsProvider
     */
    public function testLoadingConfiguration(array $options, array $expected)
    {
        $processor = new Processor();

        $result = $processor->processConfiguration(new Configuration(), $options);

        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function testContactConfigIsRequiredWithAtLeastOneContactMethod()
    {
        $options   = ['security_txt' => ['enabled' => true]];
        $processor = new Processor();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "info" at path "security_txt" must be configured.');

        $processor->processConfiguration(new Configuration(), $options);
    }

    /**
     * @see testLoadingConfiguration
     *
     * @return array
     */
    public function optionsProvider()
    {
        return [
            'default_configs' => [
                ['security_txt' => ['enabled' => false, 'info' => ['contact' => ['email' => 'mail@test.com']]]],
                [
                    'enabled'     => false,
                    'info'        => [
                        'contact' => [
                            'comment' => 'For security issues please contact us using one of the methods listed below',
                            'email'   => 'mail@test.com',
                        ],
                    ],
                    'output_to'   => 'file',
                    'path'        => '/public',
                    'create_path' => true,
                ],
            ],
            'contacts'        => [[
                'security_txt' => [
                    'enabled' => true,
                    'info'    => ['contact' => ['email' => 'mail@test.com', 'phone' => '00000000']],
                ]],
                [
                    'enabled'     => true,
                    'info'        => [
                        'contact' => [
                            'comment' => 'For security issues please contact us using one of the methods listed below',
                            'email'   => 'mail@test.com',
                            'phone'   => '00000000',
                        ],
                    ],
                    'output_to'   => 'file',
                    'path'        => '/public',
                    'create_path' => true,
                ],
            ],

        ];
    }
}
