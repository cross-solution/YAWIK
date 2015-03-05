<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */


namespace Jobs\Listener\Response;
use Core\Listener\Response\ResponseInterface;

/**
 * Class JobResponse
 * @package Jobs\Listener\Response
 */
class JobResponse implements ResponseInterface
{

    const RESPONSE_OK             = 'ok';
    const RESPONSE_FAIL           = 'fail';
    const RESPONSE_NOTIMPLEMENTED = 'notimplemeted';
    const RESPONSE_DEPRECATED     = 'deprecated';

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $status = self::RESPONSE_FAIL;

    /**
     * @param string $message
     */
    public function __construct($message = '') {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }
}