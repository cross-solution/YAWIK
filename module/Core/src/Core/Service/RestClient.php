<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
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
     * etablish all parameters to the AMS-Server
     * these are quite good default-parameter for all REST-Clients
     *
     * notice: PHP_AUTH_USER and PHP_AUTH_PW are the basic-authentification parameter,
     * they are set by setAuth($user, $pw)
     * at the client they are with available at the $_SERVER['PHP_AUTH_USER'] and $_SERVER['PHP_AUTH_PW'] again
     * @param null|string $uri
     * @param array $config
     */
    public function __construct($uri, array $config)
    {
        $this->config = $config;
        $config = ArrayUtils::merge( array(
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'keepalive' => False,
                'encodecookies' => False,
                'outputstream' => False,
                'httpversion' => Request::VERSION_11,
                'storeresponse' => False
            ),
            $config);

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
    protected function authetificate() {
        if (!array_key_exists('PHP_AUTH_USER', $this->config)) {
            throw new \RuntimeException('PHP_AUTH_USER missing', 500);
        }
        if (!array_key_exists('PHP_AUTH_PW', $this->config)) {
            throw new \RuntimeException('PHP_AUTH_PW missing', 500);
        }

        $auth = $this->config['PHP_AUTH_USER'];
        $pw = $this->config['PHP_AUTH_PW'];
        unset($this->config['PHP_AUTH_USER'], $this->config['PHP_AUTH_PW']);
        return $this->setAuth($auth, $pw);

    }
}