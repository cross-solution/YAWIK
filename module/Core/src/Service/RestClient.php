<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Service;

use Zend\Http\Client as ZendClient;
use Zend\Http\Request;
use Zend\Http\Headers;
use \Zend\Stdlib\ArrayUtils;

/**
 * Class RestClient
 * @package Core\Service
 */
class RestClient extends ZendClient
{
    protected $config;

    /**
     * establish all parameters to another YAWIK instance
     * these are quite good default-parameter for all REST-Clients
     *
     * notice: PHP_AUTH_USER and PHP_AUTH_PW are the basic-authentification parameter,
     * they are set by setAuth($user, $pw). It will be replaced by oAuth2.
     * at the client they are with available at the $_SERVER['PHP_AUTH_USER'] and $_SERVER['PHP_AUTH_PW'] again
     * @param null|string $uri
     * @param array $config
     */
    public function __construct($uri, array $config)
    {
        $this->config = $config;
        $config = ArrayUtils::merge(
            array(
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'keepalive' => false,
                'encodecookies' => false,
                'outputstream' => false,
                'httpversion' => Request::VERSION_11,
                'storeresponse' => false,
                'maxredirects' => 2
            ),
            $config
        );

        parent::__construct($uri, $config);
        $this->setEncType('application/json');
        $this->authetificate();
    }

    /**
     * @param null $key
     * @param null $module
     * @return mixed
     */
    public function __invoke($key = null, $module = null)
    {
        return $this->get($key, $module);
    }

    /**
     * Get Request
     *
     * @return Request
     */
    public function getRequest()
    {
        if (empty($this->request)) {
            $headers = new Headers();
            $headers->addHeaders(
                array(
                        'Accept' => 'application/json',
                        'Content-Type' => 'application/json'
                    )
            );
            $request = parent::getRequest();
            $request->setHeaders($headers);
            $request->setMethod('POST');
        }
        return $this->request;
    }

    /**
     * @throws \RuntimeException
     * @return mixed
     */
    protected function authetificate()
    {
        $auth = array_key_exists('user', $this->config)?$this->config['user']:'';
        $pass = array_key_exists('pass', $this->config)?$this->config['pass']:'';
        return $this->setAuth($auth, $pass);
    }

    public function getHost()
    {
        if (empty($this->config) || !array_key_exists('host', $this->config)) {
            return null;
        }
        return $this->config['host'];
    }
}
