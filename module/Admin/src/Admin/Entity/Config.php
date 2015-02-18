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

}


