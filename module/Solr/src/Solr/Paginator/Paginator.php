<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.27
 */
namespace Solr\Paginator;

use Zend\Paginator\Exception\InvalidArgumentException;
use Solr\FacetsProviderInterface;

class Paginator extends \Zend\Paginator\Paginator implements FacetsProviderInterface
{

    /**
     * @see \Zend\Paginator\Paginator::__construct()
     */
    public function __construct($adapter)
    {
        if (!$adapter instanceof FacetsProviderInterface) {
            throw new InvalidArgumentException(sprintf('adapter must implement %s interface', FacetsProviderInterface::class));
        }
        
        parent::__construct($adapter);
    }

    /**
     * @return \ArrayAccess
     */
    public function getFacets()
    {
        return $this->adapter->getFacets();
    }
}