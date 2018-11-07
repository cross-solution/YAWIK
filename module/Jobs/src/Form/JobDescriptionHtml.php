<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Jobs\Form;

use Core\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Jobs\Form\Hydrator\JobDescriptionHydrator;
use Zend\Json\Expr;

/**
 * Defines the formular field html of a job opening.
 *
 * @package Jobs\Form
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.29
 */
class JobDescriptionHtml extends Form
{
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new JobDescriptionHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }

    public function init()
    {
        $this->setName('jobs-form-html');
        $this->setAttributes(
            [
            'id' => 'jobs-form-html',
            'data-handle-by' => 'yk-form'
            ]
        );

        $this->add(
            [
            'type' => 'Textarea',
            'name' => 'description-html',
            'options' => [
                'placeholder' => /*@translate*/ 'Enter pure html code here',
            ],
                'attributes' => ['style' => 'width:100%;height:100%;',]
            ]
        );
    }
}
