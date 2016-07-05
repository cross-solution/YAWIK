<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace OrganizationsTest\Exception;

use CoreTestUtils\TestCase\AssertInheritanceTrait;
use Organizations\Exception\ExceptionInterface;
use Organizations\Exception\MissingParentOrganizationException;

/**
 * Tests for \Organizations\Exception\MissingParentOrganizationException
 * 
 * @covers \Organizations\Exception\MissingParentOrganizationException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *  
 */
class MissingParentOrganizationExceptionTest extends \PHPUnit_Framework_TestCase
{
    use AssertInheritanceTrait;

    private $target = MissingParentOrganizationException::class;

    private $inheritance = [ ExceptionInterface::class ];
}