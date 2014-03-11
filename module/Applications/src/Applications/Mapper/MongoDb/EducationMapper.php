<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb */
namespace Applications\Mapper\MongoDb;

use Core\Mapper\MongoDb\AbstractMapper;
use Core\Model\ModelInterface;
use Core\Model\Hydrator\ModelHydrator;
use Core\Mapper\MongoDb\Hydrator\DatetimeStrategy;
use Core\Mapper\MongoDb\Hydrator\ModelCollectionStrategy;
use Core\Mapper\MongoDb\MapperInterface;


/**
 * User mapper factory
 */
class EducationMapper extends AbstractMapper
{

    protected function _createCollectionFromResult($cursor)
    {
        $models = array();
        foreach ($cursor as $data) {
            foreach ($data as $item) {
                $models += $item; //$this->create($data);
            }
        }
        $models += array('description' => 'Huch');
        print_r($models);
        $collection = $this->createCollection($models);
        return $collection;
    }
    
    public function fetchByApplicationId ($id)
    {
        if (!$id instanceof \MongoId) {
            $id = new \MongoId($id);
        }
        
        $data = $this->getCollection()->findOne(array('_id' => $id), array('educations' => true));
        return $data['educations'];
        return $this->_createCollectionFromResult($cursor);
    }
}