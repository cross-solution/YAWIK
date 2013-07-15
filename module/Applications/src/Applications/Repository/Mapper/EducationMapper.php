<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb */
namespace Applications\Repository\Mapper;

use Core\Repository\Mapper\AbstractMapper;


/**
 * User mapper factory
 */
class EducationMapper extends AbstractMapper
{

   
    public function fetchByApplicationId ($id)
    {
        if (!$id instanceof \MongoId) {
            $id = new \MongoId($id);
        }
        
        $data = $this->getCollection()->findOne(array('_id' => $id), array('educations' => true));
        return $data['educations'];
   
    }
}