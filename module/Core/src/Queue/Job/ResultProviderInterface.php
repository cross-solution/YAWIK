<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2019 Cross Solution <http://cross-solution.de>
 */

declare(strict_types=1);

/** */
namespace Core\Queue\Job;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
interface ResultProviderInterface
{
    public function setResult(JobResult $error) : void;
    public function getResult() : JobResult;
}
