<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Listener\Events;

use Zend\EventManager\Event;
use Zend\Http\Request;
use Zend\Http\Response;

/**
 * Ajax route event
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class AjaxEvent extends Event
{
    /**#@+
     * Response content types.
     * @var string
     */
    const TYPE_HTML = 'text/html';
    const TYPE_JSON = 'application/json';
    const TYPE_TEXT = 'text/plain';

    /**#@-*/

    /**
     * The mvc request object
     *
     * @var Request
     */
    private $request;

    /**
     * The mvc response object
     *
     * @var Response
     */
    private $response;

    /**
     * The result.
     *
     * @var mixed|array|\Traversable
     */
    private $result;

    /**
     * The response content type.
     *
     * @var string
     */
    private $contentType;

    /**
     * Set the content type used in the response header.
     *
     * @param string $contentType
     *
     * @return self
     */
    public function setContentType($contentType)
    {
        $this->setParam('contentType', $contentType);
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * Get the content type.
     *
     * @return string
     */
    public function getContentType()
    {
        if (!$this->contentType) {
            $this->setContentType($this->getParam('contentType') ?: static::TYPE_JSON);
        }

        return $this->contentType;
    }

    /**
     * Set the mvc request.
     *
     * @param Request $request
     *
     * @return self
     */
    public function setRequest(Request $request)
    {
        $this->setParam('request', $request);
        $this->request = $request;

        return $this;
    }

    /**
     * Get the mvc request.
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request ?: $this->getParam('request');
    }

    /**
     * Set the mvc response
     *
     * @param Response $response
     *
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->setParam('response', $response);
        $this->response = $response;

        return $this;
    }

    /**
     * Get the mvc response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response ?: $this->getParam('response');
    }

    /**
     * Set the result.
     *
     * @param mixed|array|\Traversable $result
     *
     * @return self
     */
    public function setResult($result)
    {
        $this->setParam('result', $result);
        $this->result = $result;

        return $this;
    }

    /**
     * Get the result.
     *
     * @return mixed|array|\Traversable
     */
    public function getResult()
    {
        return $this->result ?: $this->getParam('result');
    }
}
