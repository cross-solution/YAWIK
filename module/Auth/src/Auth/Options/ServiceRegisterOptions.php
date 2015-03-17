<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Auth\Options;

use Zend\Stdlib\AbstractOptions;
use Core\Mail\MailService;
use Auth\Repository\User;

/**
 * Class ServiceRegisterOptions
 * @package Auth\Options
 */
class ServiceRegisterOptions extends AbstractOptions {

    /**
     * @var
     */
    protected $userRepository;

    /**
     * @var
     */
    protected $mailService;

    /**
     * @return mixed
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }

    /**
     * @param User $userRepository
     * @return $this
     */
    public function setUserRepository(User $userRepository)
    {
        $this->userRepository = $userRepository;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMailService()
    {
        return $this->mailService;
    }

    /**
     * @param MailService $mailService
     * @return $this
     */
    public function setMailService(MailService $mailService)
    {
        $this->mailService = $mailService;
        return $this;
    }
}