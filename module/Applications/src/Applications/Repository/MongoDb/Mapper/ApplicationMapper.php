<?php
/**
 * Cross Applicant Management
 *
 * @filesource
 * @copyright (c) 2013 Cross Solution (http://cross-solution.de)
 * @license   GPLv3
 */

/** Auth mapper mongodb */
namespace Applications\Repository\MongoDb\Mapper;

use Core\Repository\MongoDb\Mapper\AbstractMapper;


/**
 *
 */
class ApplicationMapper extends AbstractMapper
{

    public function fetchByJobid($jobId)
    {
        $query = array('jobId' => (string) $jobId);
        $cursor = $this->getCollection()->find($query);
        return $cursor;    
    }
    
    

      
}