<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Jobs\Form\InputFilter;

use Jobs\Entity\AtsModeInterface;
use Zend\InputFilter\Exception;
use Zend\InputFilter\InputFilter;

/**
 * InputFilter for the ATS settings.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @since 0.19
 */
class AtsMode extends InputFilter
{
    /**
     * @var
     */
    protected $host;

    /**
     * @param $host
     * @return $this
     */
    public function setHost($host)
    {
        $this->setHost = $host;
        return $this;
    }

    /**
     * Sets data for validating and filtering.
     *
     * @internal
     *  We needed to add dynamically validators, because when "mode" is "intern" or "none" we must
     *  not validate anything. When "mode" is "uri" we must not validate "email address" and we must not
     *  validate "uri" if mode is "uri".
     *
     *  And only when the data is set we do know what has to be validated.
     */
    public function setData($data)
    {
        switch ($data['mode']) {
            default:
                break;

            case AtsModeInterface::MODE_URI:
                $this->add(
                    array(
                    'name' => 'uri',
                    'validators' => array(
                        array(
                            'name' => 'uri',
                            'options' => array(
                                'allowRelative' => false,
                            ),
                        ),
                    ),
                    'filters'  => array(
                        array('name' => 'StripTags'),
                    ),
                    )
                );
                break;

            case AtsModeInterface::MODE_EMAIL:
                $this->add(
                    array(
                    'name' => 'email',
                    'validators' => array(
                        array('name' => 'EmailAddress')
                    ),
                    )
                );
                break;
        }
        
        $this->add([
            'name' => 'oneClickApplyProfiles',
            'required' => $data['mode'] == AtsModeInterface::MODE_INTERN && $data['oneClickApply']
        ]);

        return parent::setData($data);
    }
}
