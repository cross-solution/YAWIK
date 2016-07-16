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

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class InvoiceAddressDrafts extends AbstractRepository
{
    /**
     *
     *
     * @var array
     */
    protected $entities;

    public function findByJobId($jobId)
    {
        if (isset($this->entities[$jobId])) {
            return $this->entities[$jobId];
        }

        $entity = $this->findOneBy(['jobId' => $jobId]);
        $this->entities[$jobId] = $entity;

        return $entity;
    }
}