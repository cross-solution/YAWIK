<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Listener\Response;

/**
 * give Responses a more coherent handling
 * Interface ResponseInterface
 * @package Core\Listener\Responseinterface
 */
interface ResponseInterface
{
    /**
     * @return mixed
     */
    public function getMessage();
}
