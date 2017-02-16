<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Orders\Repository;

use Core\Repository\AbstractRepository;
use Orders\Entity\OrderInterface;
use Orders\Entity\OrderNumberCounter;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Orders extends AbstractRepository
{
    public function createJobOrder(array $data=[])
    {
        $data['type'] = OrderInterface::TYPE_JOB;

        return $this->create($data);
    }

    public function create(array $data=[], $counterName = null, $counterFormat = null)
    {
        if (isset($data['counter'])) {
            $counterName = $data['counter'];
            unset($data['counter']);
        }
        if (null === $counterName) {
            $counterName = date('Y');
        }

        $counter = $this->dm->createQueryBuilder('\Orders\Entity\OrderNumberCounter')
                            ->findAndUpdate()
                            ->upsert(true)->returnNew(true)
                            ->field('name')->equals($counterName)
                            ->field('name')->set($counterName)
                            ->field('count')->inc(1)
                            ->getQuery()->execute();

        $data['number'] = $counter->format();

        return parent::create($data);
    }

    public function findByJobId($jobId)
    {
        $entity = $this->findOneBy(['entity.entity.$ref' => 'jobs', 'entity.entity.$id' => new \MongoId($jobId)]);
        return $entity;
    }
}