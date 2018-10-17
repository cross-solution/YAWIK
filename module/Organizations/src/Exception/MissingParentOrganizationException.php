<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Organizations\Exception;

/**
 * Exception thrown, if a recruiter tries to create a hiring organization when parent organization is not created yet.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.25.2
 */
class MissingParentOrganizationException extends \RuntimeException implements ExceptionInterface
{
}
