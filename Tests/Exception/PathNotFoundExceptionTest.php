<?php declare(strict_types=1);

namespace KoderHut\SecurityTxtBundle\Tests\Exception;

use KoderHut\SecurityTxtBundle\Exception\PathNotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * Class PathNotFoundExceptionTest
 *
 * @author Denis-Florin Rendler <connect@rendler.me>
 *
 * @covers \KoderHut\SecurityTxtBundle\Exception\PathNotFoundException
 */
class PathNotFoundExceptionTest extends TestCase
{

    /**
     * @test
     */
    public function testExceptionIsInstanceOfRuntimeException()
    {
        $instance = new PathNotFoundException();

        $this->assertInstanceOf(\RuntimeException::class, $instance);
    }
}
