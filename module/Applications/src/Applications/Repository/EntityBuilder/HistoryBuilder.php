<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** HistoryBuilder.php */ 
namespace Applications\Repository\EntityBuilder;

use Core\Repository\EntityBuilder\RelationAwareBuilder;
use Zend\Stdlib\ArrayUtils;
use Core\Entity\CollectionInterface;

class HistoryBuilder extends RelationAwareBuilder
{
    
    protected $reverseOrder = true;
    
    public function setReverseOrder($flag)
    {
        $this->reverseOrder = (bool) $flag;
        return $this;
    }
    
    public function reverseOrder()
    {
        return $this->reverseOrder();
    }
    
    public function getCollection()
    {
        $collection = parent::getCollection();
        $collection->setEntityBuilder($this);
        return $collection;
    }
    
    public function buildCollection($data)
    {
        if ($data instanceOf \Traversable) {
            $data = ArrayUtils::iteratorToArray($data, /*recursive*/ false);
        }

        return parent::buildCollection($data);
    }
    
    public function unbuildCollection(CollectionInterface $collection)
    {
        $data = parent::unbuildCollection($collection);
        if ($this->reverseOrder) {
            $data = array_reverse($data);
        }
        return $data;
    }
}

