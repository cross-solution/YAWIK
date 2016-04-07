<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Form;

use string;
use Zend\Form\Form as ZfForm;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class TextSearchForm extends ZfForm implements HeadscriptProviderInterface
{
    protected $headscripts = [
        'Core/js/core.searchform.js'
    ];

    protected $options = [
        'button_element' => 'text',
        'placeholder'    => /* @translate */ 'Search query',
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

    public function setSearchParams($params)
    {
        $json = \Zend\Json\Json::encode($params);
        $this->setAttribute('data-search-params', $json);

        return $this;
    }

    public function init()
    {
        $this->setName('search');
        $this->setAttributes([
                                 'data-handle-by' => 'native',
                                 'method' => 'get',
                                 'class' => 'form-inline search-form',
                             ]);

        $this->add([
                       'type' => 'Text',
                       'name' => 'text',
                       'options' => [
                           'label' => /*@translate*/ 'Search',
                           'use_formrow_helper' => false,
                       ],
                       'attributes' => [
                           'class' => 'form-control',
                           'placeholder' => $this->getOption('placeholder'),
                       ]
                   ]);


    }
}