<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Jobs forms */
namespace Jobs\Form;

use Core\Form\Event\FormEvent;
use Core\Form\WizardContainer;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerInterface;
use Zend\View\Renderer\PhpRenderer as Renderer;
use Core\Form\propagateAttributeInterface;

/**
 * Jobs form container. Defines all formulars for entering a job position.
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */
class Job extends WizardContainer
{

    /**
     * The event manager
     *
     * @var EventManagerInterface
     */
    protected $events;

    public function setEventManager(EventManagerInterface $events)
    {
        $this->events = $events;

        return $this;
    }

    public function getEventManager()
    {
        if (!$this->events) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    /**
     * {@inheritDoc}
     *
     * Adds the standard forms and child containers.
     *
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $elements = [
            'general' => [
                'priority' => 100,
                'label' => /*@translate*/ 'Basic Data',
                'options' => [
                    'label' => /*@translate*/ 'Basic Data'
                ],
                'property' => true,
                'forms' => [

                    'locationForm' => array(
                        'type' => 'Jobs/Base',
                        'property' => true,
                        'options' => array(
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Please choose a descriptive title and a location for your job posting ',
                            'display_mode' => 'summary'
                        )
                    ),
                    'nameForm' => array(
                        'type' => 'Jobs/CompanyName',
                        'property' => true,
                        'options' => array(
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Please choose the name of the hiring organization. The selected name defines the template of the job opening.',
                            'display_mode' => 'summary'
                        )
                    ),
                    'salaryForm' => array(
                        'type' => 'Jobs/Salary',
                        'property' => 'salary',
                        'options' => array(
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Please choose a salary of your job opening.',
                            'display_mode' => 'summary'
                        )
                    ),
                    'classifications' => [
                        'type' => 'Jobs/Classifications',
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Classify the job.',
                            'display_mode' => 'summary',
                        ],
                    ],
                    'portalForm' => array(
                        'type' => 'Jobs/Multipost',
                        'property' => true,
                        'options' => array(
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'Please choose the portals, where you wish to publish your job opening.',
                            'display_mode' => 'summary'
                        )
                    ),
                    'customerNote' => [
                        'type' => 'Jobs/CustomerNote',
                        'property' => true,
                        'options' => [
                            'enable_descriptions' => true,
                            'description' => /*@translate*/ 'If there is something you want us to know about this job offering, you can type it here.',
                            'display_mode' => 'summary'
                        ]
                    ]
                ],
            ],

            'description' => [
                'priority' => '80',
                'options' => [ 'label' => /*@translate*/ 'Create job opening' ],
                'property' => true,
                'forms' => [
                    'descriptionForm' => array(
                        'type' => 'Jobs/Description',
                        'property' => true,
                    ),
                ],
            ],

            'preview' => [
                'priority' => 60,
                'options' => [ 'label' => /*@translate*/ 'Preview' ],
                'property' => true,
                'forms' => [
                    'previewForm' => array(
                        'type' => 'Jobs/Preview',
                        'property' => true,
                    ),
                ],
            ],

        ];

        $this->setForms($elements);

        $events  = $this->getEventManager();
        $events->trigger(FormEvent::EVENT_INIT, $this);
    }

    public function renderPost(Renderer $renderer)
    {
        $coreformsjs   = $renderer->basepath('modules/Core/js/core.forms.js');
        $javaScript = <<<JS
        $(document).ready(function() {

            console.log('attached yk.forms.done to ', \$('form'));

             \$('form').on('yk.forms.done', function(event, data) {
                //if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {}
                if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {
                    if (typeof data['data']['jobvalid'] != 'undefined' && data['data']['jobvalid'] === true) {
                        $('#job_incomplete').hide();
                        \$('.wizard-container .finish').removeClass('disabled');
                    }
                    else {
                        $('#job_incomplete').show();
                        \$('.wizard-container .finish').addClass('disabled');
                    }
                }
                \$('#job_errormessages').empty();

                if (typeof data['data']['errorMessage'] != 'undefined') {
                    $('#job_errormessages').append(data['data']['errorMessage']);
                }
                console.debug('job-form-inline', event, data);
             });
             \$('.wizard-container').on('wizard:tabShow.jobcontainer', function(e, \$tab, \$nav, index) {
                var \$link = \$tab.find('a');
                var href = \$link.attr('href');
                var \$target = \$(href);
                var \$iframe = \$target.find('iframe');

                \$iframe.each(function() { this.contentDocument.location.reload(true); });

                var \$productList = \$target.find('#product-list-wrapper');
                if (\$productList.length) {
                    \$productList.html('').load('/' + lang + '/jobs/channel-list?id=' + \$('#general-nameForm-job').val());
                }
             });

             \$('.wizard-container .finish a').click(function (e) {
                if (\$(e.currentTarget).parent().hasClass('disabled')) {
                    e.preventDefault();
                    return false;
                }
             });

        });
JS;

        $renderer->headScript()->appendScript($javaScript);

        return parent::renderPost($renderer);
    }


    public function enableAll($enable = true)
    {
        foreach ($this->activeForms as $formkey) {
            $forms = $this->getForm($formkey);
            if ($forms instanceof propagateAttributeInterface) {
                $forms->enableAll($enable);
            }
        }
        return $this;
    }
}
