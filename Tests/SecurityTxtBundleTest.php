<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\Tests;

use KoderHut\SecurityTxt\SecurityTxt;
use KoderHut\SecurityTxtBundle\SecurityTxtBundle;
use Nyholm\BundleTest\BaseBundleTestCase;

/**
 * Class SecurityTxtBundleTest
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 *
 * @covers \KoderHut\SecurityTxtBundle\SecurityTxtBundle
 */
class SecurityTxtBundleTest extends BaseBundleTestCase
{

    /**
     * @inheritdoc
     */
    protected function getBundleClass()
    {
        return SecurityTxtBundle::class;
    }

    /**
     * @test
     */
    public function testSecurityTxtServiceIsRegistered()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__ . '/Framework/empty_config.yaml');
        $this->bootKernel();

        $document = $this->getContainer()->get(SecurityTxt::class);

        $this->assertEquals('# Test comment' . PHP_EOL . 'Contact: mailto:test@email.com' . PHP_EOL . PHP_EOL, $document->__toString());
    }

    /**
     * @test
     */
    public function testContainerHasInfoConfigs()
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__ . '/Framework/config.yaml');
        $this->bootKernel();

        $expected = [
            'contact'          =>
                [
                    'email'   => 'mailto:security@example.com',
                    'phone'   => 'tel:1234567890',
                    'url'     => 'https://test.com',
                    'comment' => 'For security issues please contact us using one of the methods listed below',
                ],
            'encryption'       =>
                [
                    'url'     => 'https://example.com/pgp-key.txt',
                    'comment' => 'Our encryption key',
                ],
            'policy'           =>
                [
                    'url'     => 'https://example.com/security-policy.html',
                    'comment' => 'Our policy on security issues disclosure',
                ],
            'acknowledgements' =>
                [
                    'url'     => 'https://example.com/hall-of-fame.html',
                    'comment' => 'Out hall-of-fame',
                ],
            'signature'        =>
                [
                    'url'     => 'https://example.com/.well-known/security.txt.sig',
                    'comment' => 'Our signature file',
                ],
        ];

        $this->assertTrue($this->getContainer()->hasParameter('security_txt.info'));

        $configs = $this->getContainer()->getParameter('security_txt.info');

        $this->assertEquals($expected, $configs);
    }
}
