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
        $data = array_reverse($data);
        return $data;
    }
}

