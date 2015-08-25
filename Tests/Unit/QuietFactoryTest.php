<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Unit;

use Symfony\Cmf\Bundle\MenuBundle\QuietFactory;

class QuietFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $innerFactory;
    private $logger;

    protected function setUp()
    {
        $this->innerFactory = $this->prophesize('Knp\Menu\FactoryInterface');
        $this->logger = $this->prophesize('Psr\Log\LoggerInterface');
    }

    public function testAllowEmptyItemsReturnsItemWithoutURL()
    {
        $this->innerFactory->createItem('Home', array('route' => 'not_existent'))
            ->willThrow('Symfony\Component\Routing\Exception\RouteNotFoundException');

        $this->innerFactory->createItem('Home', array())->shouldBeCalled();

        $factory = new QuietFactory($this->innerFactory->reveal(), $this->logger->reveal(), true);

        $factory->createItem('Home', array('route' => 'not_existent'));
    }
}
