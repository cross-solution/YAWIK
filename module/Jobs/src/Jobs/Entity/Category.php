<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Entity;

use Core\Entity\Tree\Node;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Job Category
 *
 * Currently used for "Professions", "Industries" and "EmploymentTypes"
 *
 * @ODM\Document(collection="jobs.categories", repositoryClass="Jobs\Repository\Categories")
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class Category extends Node
{
    
}