<?php

namespace Applications\Filter;

use Zend\Filter\FilterInterface;

class ParamsToProperties implements FilterInterface
{

    public function __construct($jobMapper, $auth)
    {
        $this->jobMapper = $jobMapper;
        $this->auth = $auth;
    }
    
    public function filter($value)
    {
        $properties = array();
        
        if (isset($value["applyId"])) {
            $job = $this->jobRepository->findByApplyId($value["applyId"]);
            if ($job) {
                $properties['jobId'] = $job->id;
            } else {
                $properties['jobId'] = "xxxNOTHERExxx";
            }
        }

        if (isset($value['by']) && 'me' != $value['by']) {
            switch ($value['by']) {
                case "jobs":
                    
//                     $jobs = $this->jobMapper->getCursor(
//                         array('userId' => $this->auth->getUser()->id),
//                         array('_id')
//                     );
                    
//                     $jobIds = array_map(
//                         function($a) { return (string) $a['_id']; },
//                         iterator_to_array($jobs)
//                     );
                    
//                     $properties['jobId'] = array('$in' => $jobIds);
                    $properties['refs.jobs.userId'] = $this->auth->getUser()->id;
                    break;
                    
                default:
                    break;
            }
        } else {
            $properties['userId'] = $this->auth->getUser()->id;
        }
        
        
        return $properties;
    }
}