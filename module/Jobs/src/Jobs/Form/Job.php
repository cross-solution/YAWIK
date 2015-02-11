<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Jobs forms */
namespace Jobs\Form;

use Core\Form\Container;
use Zend\View\Renderer\PhpRenderer as Renderer;
use Core\Form\propagateAttributeInterface;

/**
 * Jobs form container. Defines all formulars for entering a job position.
 *
 * @author Mathias Weitz <weitz@cross-solution.de>
 */
class Job extends Container
{

    /**
     * {@inheritDoc}
     *
     * Adds the standard forms and child containers.
     *
     * @see \Zend\Form\Element::init()
     */
    public function init()
    {
        $this->setForms(array(
            'locationForm' => array(
                'type' => 'Jobs/Base',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please choose a descriptive title and a location for your job posting ',
                    'display_mode' => 'summary'
                )
            )
        ));

        $this->setForms(array(
            'nameForm' => array(
                'type' => 'Jobs/CompanyName',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please choose the name of the hiring organization. The selected name defines the template of the job opening.',
                    'display_mode' => 'summary'
                )
            )
        ));


        $this->setForms(array(
            'portalForm' => array(
                'type' => 'Jobs/Multipost',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                    'description' => /*@translate*/ 'Please choose the portals, where you wish to publish your job opening.',
                    'display_mode' => 'summary'
                )
            )
        ));



        $this->setForms(array(
            'descriptionForm' => array(
                'type' => 'Jobs/Description',
                'property' => true,
            )
        ));


        $this->setForms(array(
            'previewForm' => array(
                'type' => 'Jobs/Preview',
                'property' => true,
            )
        ));

    }

    public function renderPost(Renderer $renderer) {
        $coreformsjs   = $renderer->basepath('/Core/js/core.forms.js');
        $javaScript = <<<JS
        $(document).ready(function() {

            console.log('attached yk.forms.done to ', $('form'));

             $('form').on('yk.forms.done', function(event, data) {
                //if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {}
                if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {
                    if (typeof data['data']['jobvalid'] != 'undefined' && data['data']['jobvalid'] === true) {
                        $('#job_incomplete').hide();
                        $('#finalize_jobentry').show();
                    }
                    else {
                        $('#job_incomplete').show();
                        $('#finalize_jobentry').hide();
                    }
                }
                $('#job_errormessages').empty();

                if (typeof data['data']['errorMessage'] != 'undefined') {
                    $('#job_errormessages').append(data['data']['errorMessage']);
                }
                console.debug('job-form-inline', event, data);
             });

        });
JS;

        $renderer->headScript()->appendScript($javaScript);
        $renderer->headScript()->appendFile($renderer->basePath('/Jobs/js/form.companyname.js'));

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
