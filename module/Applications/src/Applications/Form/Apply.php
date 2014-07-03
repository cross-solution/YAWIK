<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2014 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/**  */ 
namespace Applications\Form;

use Core\Form\Container;
use Core\Entity\EntityInterface;
/**
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */

class Apply extends Container
{
    protected $forms = array(
        'contact' => 'Auth/UserInfoContainer',
        'base'    => array(
            'type' => 'Applications/Base',
            'property' => true,
        ),
    );
    
    public function init()
    {
        //$this->setName('application');
    }
    
    public function setEntity(EntityInterface $entity)
    {
        $this->setParam('applicationId', $entity->id);
        return parent::setEntity($entity);
    }
}
