<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Options;

use \Zend\Mail\Transport\SmtpOptions;

/**
 * ${CARET}
 * 
 * @author Carsten Bleek <bleek@cross-solution.de>
 */
class MailServiceOptions extends SmtpOptions
{
    /**
     * @var string Local client hostname
     */
    protected $name = 'localhost';

    /**
     * @var string
     */
    protected $connectionClass = 'smtp';

    /**
     * Connection configuration (passed to the underlying Protocol class)
     *
     * @var array
     */
    protected $connectionConfig = [];

    /**
     * @var string Remote SMTP hostname or IP
     */
    protected $host = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 25;

    /**
     * @var string
     */
    protected $ssl;

    /**
     * @var string username
     */
    protected $username;

    /**
     * @var string password
     */
    protected $password;


    public function setUsername($username) {
        if ($username) {
            $this->connectionConfig['username'] = $username;
        }
        return $this;
    }

    public function getUsername(){
        return $this->connectionConfig['username'];
    }

    public function setPassword($password) {
        if ($password) {
            $this->connectionConfig['password'] = $password;
        }
        return $this;
    }

    public function getPassword(){
        return $this->connectionConfig['password'];
    }

    public function setSsl($ssl) {
        if ($ssl) {
            $this->connectionConfig['ssl'] = $ssl;
        }
        return $this;
    }

    public function getSsl(){
        return $this->connectionConfig['ssl'];
    }
}