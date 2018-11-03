<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright 2013 - 2016 Cross Solution <http://cross-solution.de>
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
    /**
     * @var array $baseFieldset
     */
    protected $baseFieldset = array(
        'type' => 'Applications/FactsFieldset',
        'options' => array(
            'is_disable_capable' => false,
            'is_disable_elements_capable' => true,
        )
    );

    protected $displayMode = self::DISPLAY_SUMMARY;

    /**
     * initialize facts form
     */
    public function init()
    {
        $this->options['disable_capable'] = array(
            'description' => /*@translate*/ 'Request additional facts from the Applicant. Selected Fields will be added to the application Form.',
        );
        $this->setLabel('Facts');
        $this->setIsDescriptionsEnabled(true);
        $this->setDescription(/*@translate*/ 'Please provide some additional facts for this job opening.');
        $this->setIsDisableCapable(true);
        $this->setIsDisableElementsCapable(true);

        parent::init();
    }
}
