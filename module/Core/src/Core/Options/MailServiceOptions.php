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

use Zend\Mail\Transport\FileOptions;
use Zend\Mail\Transport\SmtpOptions;

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
     * Mail transport. Possible Values "smtp", "sendmail","file".
     * If "sendmail" is used, YAWIK will use the php mail() function
     * for sending mails. This requires a local MTA.
     *
     * If "file" is used, YAWIK will use FileTransport class
     * to be able to test mail functionality
     *
     * https://docs.zendframework.com/zend-mail/transport/intro/#configuration-options
     *
     * @var string $transportClass
     */
    protected $transportClass = 'sendmail';

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

    /**
     * @var string Path to stored mail files
     * @see FileOptions::setPath()
     */
    protected $path;


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

    /**
     * @return string
     */
    public function getPath()
    {
        if(is_null($this->path) || false == $this->path){
            $this->setPath(sys_get_temp_dir().'/yawik/mails');
        }
        return $this->path;
    }

    /**
     * @param string $path
     * @return MailServiceOptions
     */
    public function setPath($path)
    {
        if(!is_dir($path) && false !== $path){
            mkdir($path,0777,true);
            chmod($path,0777);
        }
        $this->path = $path;
        return $this;
    }
}
