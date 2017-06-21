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
     * Local client hostname. used in the elho chalange
     *
     * @var string $name
     */
    protected $name = 'localhost';

    /**
     * Fully-qualified classname or short name resolvable via Zend\Mail\Protocol\SmtpLoader.
     * Typically, this will be one of “smtp”, “plain”, “login”, or “crammd5”, and defaults to “smtp”.
     *
     * @var string $connectionClass
     */
    protected $connectionClass = 'plain';

    /**
     * Mail transport. Possible Values "smtp", "sendmail". If "sendmail" is used, YAWIK will use the php mail() function
     * for sending mails. This requires a local MTA.
     * https://docs.zendframework.com/zend-mail/transport/intro/#configuration-options
     *
     * @var string $transportClass
     */
    protected $transportClass = 'smtp';

    /**
     * Connection configuration (passed to the underlying Protocol class)
     *
     * @var array
     */
    protected $connectionConfig = [];

    /**
     * Remote hostname or IP address; defaults to “127.0.0.1”.
     *
     * @var string $host
     */
    protected $host = '127.0.0.1';

    /**
     * @var int
     */
    protected $port = 25;

    /**
     * 'tls', 'ssl' or null
     *
     * @var string $ssl
     */
    protected $ssl;

    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var string $password
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

    public function setTransportClass($transportClass) {
        $this->transportClass = $transportClass;
        return $this;
    }

    public function getTransportClass(){
        return $this->transportClass;
    }
}