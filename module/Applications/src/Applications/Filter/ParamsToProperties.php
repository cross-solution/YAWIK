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
            if (!isset($value['by']) || 'jobs' != $value['by']) {
                $value['by'] = "jobs";
            }
            
            $job = $this->jobRepository->findByApplyId($value["applyId"]);
            if ($job) {
                $properties['jobId'] = $job->id;
            } else {
                $properties['jobId'] = "xxxNOTHERExxx";
            }
        }

        if ($this->auth->getUser()->getRole()=='recruiter') {
            /*
             * a recruiter can see applications, which are related to his jobs
             */
            if (isset($value['by']) && 'new' === $value['by']) {
                $properties['readBy'] = array('$ne' => $this->auth->getUser()->id);
            }          
            $properties['refs.jobs.userId'] = $this->auth->getUser()->id;

        } else {
            /*
             * an applicant can see his own applications
             */
            $properties['refs.users.id'] = $this->auth->getUser()->id;
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
                
            case "dateCreated":
                $sortProp = "dateCreated.date";
                break;
                
            case "status":
                $sortProp = "status.order";
                break;
                
            default:
                break;
        }
        
        return array($sortProp => $sortDir);
        
    }
}