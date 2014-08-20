<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Auth mapper mongodb */
namespace Applications\Mapper\MongoDb;

use Core\Mapper\MongoDb\AbstractMapper;
use Core\Model\ModelInterface;
use Core\Model\Hydrator\ModelHydrator;
use Core\Mapper\MongoDb\Hydrator\DatetimeStrategy;
use Core\Mapper\MongoDb\Hydrator\ModelCollectionStrategy;


/**
 * User mapper factory
 */
class ApplicationMapper extends AbstractMapper
{

    

	public function getModelHydrator()
    {
        if (!$this->modelHydrator) {
            
            $hydrator = new \Core\Model\Hydrator\ModelHydrator();
            $hydrator->addStrategy('dateCreated', new DatetimeStrategy())
                     ->addStrategy('dateModified', new DatetimeStrategy(/*extractResetDate*/ true));
            $this->setModelHydrator($hydrator);
        }
        return $this->modelHydrator;
    }
    
    public function fetchByJobid($jobId)
    {
        $query = array('jobId' => (string) $jobId);
        $cursor = $this->_collection->find($query);
        return $this->_createCollectionFromResult($cursor);    
    }

    public function testSave()
    {
        $this->_collection->save(array(
            '_id' => $this->_getMongoId('519f6fdb81896eb844000000'),
            'firstname' => 'Yess',
            'educations' => array(
                array(
                    'id' => 'JuYallah!',
                    'description' => 'Popel2',
                )
            ),
        ));
    }
    
    public function testHydrator()
    {
        $hydrator = $this->getModelHydrator();
        $hydrator->addStrategy('educations', new \Core\Mapper\MongoDb\Hydrator\SubDocumentsStrategy($this->getEducationMapper()));
        $this->setModelHydrator($hydrator);
        $educations = new \Core\Model\Collection();
        $ed1 = new \Applications\Model\Education();
        $ed1->setId('ed1');
        $educations->addModel($ed1);
        $model = $this->create(array(
            'id' => 'test',
            'educations' => $educations,
        ));
        $data = $hydrator->extract($model);
        echo "<pre>";
        var_dump($this);
        print_r($model);
        print_r($data);
        exit;    
    }
    
   
}