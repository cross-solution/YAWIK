<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
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

    /**
     * Job has been published
     */
    const RESPONSE_OK             = 'ok';

    /**
     * Job has been published and has stopped all other publishing
     */
    const RESPONSE_OKANDSTOP      = 'ok and publishing terminated afterwards';

    /**
     * publishing has been stopped
     */
    const RESPONSE_STOP           = 'publishing terminated';

    /**
     * publishing has been denied,
     * this is very likely when a job was not intended to be sended to a portal
     */
    const RESPONSE_DENIED         = 'denied';

    /**
     * a connection to the portal could not be established
     * or the publishing of the Job has been rejected for other reasons
     */
    const RESPONSE_FAIL           = 'fail';

    /**
     * This error has nothing to do with wrong inputs,
     * something just has happend in the programm
     */
    const RESPONSE_ERROR          = 'internal Error';

    /**
     * nothing happens, but that's not a failure
     * this can imply, that this part is under maintainance or temporarely out of service and it will be ok some times later
     */
    const RESPONSE_NOTIMPLEMENTED = 'notimplemeted';

    /**
     * nothing happens, and get used to it
     */
    const RESPONSE_DEPRECATED     = 'deprecated';

    /**
     * @var string
     */
    protected $portal;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $status = self::RESPONSE_FAIL;

    /**
     * @param string $portal
     * @param string $status
     */
    public function __construct($portal = '', $status = self::RESPONSE_NOTIMPLEMENTED, $message='')
    {
        $this->portal = $portal;
        $this->status = $status;
        $this->message = $message;
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getPortal()
    {
        return $this->portal;
    }
}
