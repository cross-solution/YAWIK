<?php
/**
 * YAWIK
 *
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

namespace Admin\Entity;

use Core\Entity\AbstractIdentifiableModificationDateAwareEntity as BaseEntity;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Core\Repository\DoctrineMongoODM\Annotation as Cam;


/**
 * The global Configuration.
 *
 * @ODM\Document(collection="Configuration", repositoryClass="Admin\Repository\Configuration")
 */
class Config extends BaseEntity {

    const postConstruct = 'postRepositoryConstruct';

    protected $name;

    protected $value;

    /**
     * Sets the name of the organization
     *
     * @param string $name
     * @return ConfigInterface
     */
    public function setName($name){
        $this->name=$name;
    }

    /**
     * Gets the name of the configuration parameter
     *
     * @return string $name
     */
    public function getName() {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getValue(){
        return $this->value;
    }

    /**
     * @param string $description
     * @return ConfigInterface
     */
    public function setValue($value){
        $this->value=$value;
    }



}


