<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** */
namespace Core\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @see \Core\Entity\IdentifiableEntityInterface
 */
trait IdentifiableEntityTrait
{
       
    /**
     * Entity id
     *
     * @var string|\MongoId
     * @ODM\Id
     */
    protected $id;
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }
}
