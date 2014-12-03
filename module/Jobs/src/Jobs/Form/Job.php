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

/**
 * Jobs forms container
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
                'type' => 'Jobs/TitleLocation',
                'property' => true,
                'options' => array(
                    'enable_descriptions' => true,
                )
            )
        ));

        /*
        $this->setForms(array(
            'employersForm' => array(
                'type' => 'Jobs/Employers',
                'property' => true,
            )
        ));
        */

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

        // This label is used on the Settings page
        //$this->options['settings_label'] = /*@translate*/ 'Customize apply form';
    }

    public function renderPost(Renderer $renderer) {
        $coreformsjs   = $renderer->basepath('/Core/js/core.forms.js');
        $javaScript = <<<JS
        $(document).ready(function() {

            console.log('attached yk.forms.done to ', $('form'));

             $('form').on('yk.forms.done', function(event, data) {
                //if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {}
                if (typeof data != 'undefined' && typeof data['data'] != 'undefined') {
                    if (typeof data['data']['valid'] != 'undefined' && data['data']['valid'] === true) {
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
        return parent::renderPost($renderer);
    }

}
