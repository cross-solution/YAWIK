<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013-2014 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Applications\Form;

use Core\Form\SummaryForm;

/**
 * Facts form.
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de> 
 */
class Facts extends SummaryForm
{
    protected $baseFieldset = array(
        'type' => 'Applications/FactsFieldset',
        'options' => array(
            'is_disable_capable' => false,
            'is_disable_elements_capable' => true,
        )
    );

    protected $displayMode = self::DISPLAY_SUMMARY;

    public function init()
    {
        $this->options['disable_capable'] = array(
            'description' => /*@translate*/ 'Allow the user to provide some facts. Currently this will be only the expected salary.',
        );
        $this->setLabel('Facts');
        $this->setIsDescriptionsEnabled(true);
        $this->setDescription(/*@translate*/ 'Provide some facts the recruiter might want to know.');
        $this->setIsDisableCapable(true);
        $this->setIsDisableElementsCapable(false);

        parent::init();
    }

}