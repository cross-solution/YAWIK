<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Core\Form;

/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface EmptySummaryAwareInterface
{
    public function isSummaryEmpty();
    public function getEmptySummaryNotice();
    public function setEmptySummaryNotice($message);
}
