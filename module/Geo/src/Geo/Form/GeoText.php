<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ApplyIdentifierElement.php */ 
namespace Geo\Form;

use Zend\Form\Element\Text;
use Core\Form\ViewPartialProviderInterface;

class GeoText extends Text implements ViewPartialProviderInterface
{
    
    protected $partial = 'geo/form/GeoText';
    
    public function getViewPartial()
    {
        return $this->partial;
    }
    
    public function setViewPartial($partial)
    {
        $this->partial = $partial;
        return $this;
    }
}

