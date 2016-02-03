<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Salutation extends AbstractHelper
{

    /**
     * returns the advertised title
     *
     * @param string $gender
     * @return string
     */
    public function __invoke($gender)
    {
        $return="";
        switch ($gender) {
            case "male":
                $return = "Mr.";
                break;
            case "female":
                $return = "Mrs.";
                break;
        }
        return $return;
    }
}
