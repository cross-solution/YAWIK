<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Geo\Form;

use Core\Form\HeadscriptProviderInterface;
use Zend\Form\Element\Select;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class GeoSelect extends Select implements HeadscriptProviderInterface
{

    /**
     * @var bool
     */
    protected $disableInArrayValidator = true;

    private $headscripts = [
        'Geo/js/geoselect.js'
    ];

    /**
     * Sets the array of script names.
     *
     * @param string[] $scripts
     *
     * @return self
     */
    public function setHeadscripts(array $scripts)
    {
        $this->headscripts = $scripts;

        return $this;
    }

    /**
     * Gets the array of script names.
     *
     * @return string[]
     */
    public function getHeadscripts()
    {
        return $this->headscripts;
    }


    public function init()
    {
        $this->setAttributes([
                'data-placeholder' => /*@translate*/ 'Location',
                'data-autoinit' => false,
                'class' => 'geoselect',
        ]);

    }
}