<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** UniqueApplyId.php */
namespace Jobs\Form\Validator;

use Zend\Validator\AbstractValidator;
use Jobs\Repository\Job as JobRepository;

class UniqueApplyId extends AbstractValidator
{
    const MSG_NOT_UNIQUE = 'msgNotUnique';
    
    protected $messageTemplates = array(
        self::MSG_NOT_UNIQUE => /*@translate*/ 'The apply identifier "%value%" is already in use.'
    );
    
    public function __construct($options = null)
    {
        if ($options instanceof JobRepository) {
            $options = array('repository' => $options);
        }
        
        parent::__construct($options);
    }
    
    public function setRepository(JobRepository $repository)
    {
        $this->repository = $repository;
        return $this;
    }
    
    public function getRepository()
    {
        return $this->repository;
    }
    
    public function isValid($value)
    {
        $repository = $this->getRepository();
        if (!$repository) {
            trigger_error('Could not check uniqueness of apply id: No Repository set. Assume unique', E_USER_NOTICE);
            return true;
        }
        
        if ($repository->existsApplyId($value)) {
            $this->error(self::MSG_NOT_UNIQUE, $value);
            return false;
        }
        
        return true;
    }
}
