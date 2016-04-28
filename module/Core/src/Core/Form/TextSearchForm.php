<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Core\Form;

use Zend\Form\Form as ZfForm;

/**
 * Simple Form for result list filtering.
 *
 * Should be used with the searchForm view helper.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since  0.25
 */
class TextSearchForm extends ZfForm implements HeadscriptProviderInterface
{
    /**
     * Headscripts to be injected in the view.
     *
     * @var array
     */
    protected $headscripts = [
        'Core/js/core.searchform.js'
    ];

    /**
     * Element options.
     *
     * @var array
     */
    protected $options = [
        'button_element' => 'text',
        'placeholder'    => /* @translate */
            'Search query',
    ];

    public function getHeadscripts()
    {
        return $this->headscripts;
    }

    public function setHeadscripts(array $scripts)
    {
        $this->headscripts = $scripts;

        return $this;
    }

    /**
     * Sets the default search params attribute.
     *
     * @param array $params
     *
     * @return self
     */
    public function setSearchParams(array $params)
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
                                 'method'         => 'get',
                                 'class'          => 'form-inline search-form',
                             ]
        );

        $this->add([
                       'type'       => 'Text',
                       'name'       => 'text',
                       'options'    => [
                           'label'              => /*@translate*/
                               'Search',
                           'use_formrow_helper' => false,
                       ],
                       'attributes' => [
                           'class'       => 'form-control',
                           'placeholder' => $this->getOption('placeholder'),
                       ]
                   ]
        );


    }
}