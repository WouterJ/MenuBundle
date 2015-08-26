<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\FactoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * A decoration of the MenuFactory to not break
 * the page if a url could not be generated.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class QuietFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $innerFactory;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * Whether to return null (if value is false) or a MenuItem
     * without any URL (if value is true) if no URL can be found
     * for a MenuNode.
     *
     * @var bool
     */
    private $allowEmptyItems;

    public function __construct(FactoryInterface $innerFactory, LoggerInterface $logger = null, $allowEmptyItems = false)
    {
        $this->innerFactory = $innerFactory;
        $this->logger = $logger;
        $this->allowEmptyItems = $allowEmptyItems;
    }

    /**
     * {@inheritDoc}
     */
    public function createItem($name, array $options = array())
    {
        try {
            return $this->innerFactory->createItem($name, $options);
        } catch (RouteNotFoundException $e) {
            if (null !== $this->logger) {
                $this->logger->error(
                    sprintf('An exception was thrown while creating a menu item called "%s"', $name),
                    array('exception' => $e)
                );
            }

            if (!$this->allowEmptyItems) {
                return null;
            }

            // set linkType to uri
            $options['linkType'] = 'uri';

            return $this->innerFactory->createItem($name, $options);
        }
    }
}
