<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace OrganizationsTest\Exception;

use PHPUnit\Framework\TestCase;

use CoreTestUtils\TestCase\TestInheritanceTrait;
use Organizations\Exception\ExceptionInterface;
use Organizations\Exception\MissingParentOrganizationException;

/**
 * Tests for \Organizations\Exception\MissingParentOrganizationException
 *
 * @covers \Organizations\Exception\MissingParentOrganizationException
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MissingParentOrganizationExceptionTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = MissingParentOrganizationException::class;

    private $inheritance = [ ExceptionInterface::class ];
}
