<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form;

use Core\Form\Form;
use Zend\Hydrator\Strategy\ClosureStrategy;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test
 */
class AdminJobEdit extends Form
{
    public function init()
    {
        $this->setName('admin-job-edit');
        $this->setDescription(sprintf(
            /*@translate*/ 'Change status or publish date.%1$s%2$sBeware!%3$s Status changes will eventually cause notification emails to be send.',
            '<br><br>',
            '<strong>',
            '</strong>'
        ));

        $this->add([
                       'type' => 'Jobs/StatusSelect',

                   ]);

        $this->add([
                       'type' => 'Core/Datepicker',
                       'name' => 'datePublishStart',
                       'options' => [
                           'label' => /*@translate*/ 'Start date',
                           'description' => /*@translate*/ 'Set the start date of this job.',
                       ],
                       'attributes' => [
                           'data-date-autoclose' => 'true',
                       ],
                   ]);
        $this->setIsDescriptionsEnabled(true);
    }

    protected function addHydratorStrategies($hydrator)
    {
        parent::addHydratorStrategies($hydrator);

        $statusStrategy = new ClosureStrategy(
            /* extract */
            function ($object) {
                return $object->getName();
            }
        );

        $hydrator->addStrategy('status', $statusStrategy);
    }
}
