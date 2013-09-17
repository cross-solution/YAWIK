<?php

namespace Applications\Filter;

use Zend\Filter\FilterInterface;

class ParamsToProperties implements FilterInterface
{

    public function __construct($jobRepository, $auth)
    {
        $this->jobRepository = $jobRepository;
        $this->auth = $auth;
    }
    
    public function filter($value)
    {
        $properties = array();
        
        if (isset($value['sort'])) {
            $properties['sort'] = $this->filterSort($value['sort']);
        }
         
        
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
    
    protected function filterSort($sort)
    {
        if ('-' == $sort{0}) {
            $sortProp = substr($sort, 1);
            $sortDir  = -1;
        } else {
            $sortProp = $sort;
            $sortDir = 1;
        }
        switch ($sortProp) {
            case "date":
                $sortProp = "dateModified.date";
                break;
                
            default:
                break;
        }
        
        return array($sortProp => $sortDir);
        
    }
}