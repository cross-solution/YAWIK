<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Solr\Exception;

use Core\Exception\ExceptionInterface;

/**
 * This Exception will be thrown when Yawik fail to communicate with server
 * during add document, or query
 *
 * @author Anthonius Munthi <me@itstoni.com>
 * @package Solr\Exception
 * @since   0.26
 */
class ServerException extends \RuntimeException implements ExceptionInterface
{
}