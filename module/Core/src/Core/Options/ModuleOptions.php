<?php
/**
 * YAWIK
 * 
 * @filesource
 * @copyright (c) 2013-2015 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class ModuleOptions
 *
 * Default options of the Core Module
 *
 * @package Core\Options
 */
class ModuleOptions extends AbstractOptions {

    /**
     * The sitename is used in Mails. Typically it's the name of your website
     */
    protected $siteName="YAWIK";

    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
        return $this;
    }

    public function getSiteName()
    {
        if (empty($this->siteName)) {
                throw new \InvalidArgumentException(
                    'the argument sitename has to be defined'
                );
        }
        return $this->siteName;
    }

}