<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace CoreTest\Queue\Controller;

use PHPUnit\Framework\TestCase;

use Core\Queue\Controller\MongoQueueController;
use CoreTestUtils\TestCase\TestInheritanceTrait;
use SlmQueue\Controller\AbstractWorkerController;

/**
 * Tests for \Core\Queue\Controller\MongoQueueController
 *
 * @covers \Core\Queue\Controller\MongoQueueController
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 *
 */
class MongoQueueControllerTest extends TestCase
{
    use TestInheritanceTrait;

    private $target = [
        MongoQueueController::class,
        'as_reflection' => true,
    ];

    private $inheritance = [ AbstractWorkerController::class ];
}
