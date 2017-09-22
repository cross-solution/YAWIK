<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

namespace Auth\Form;

use Core\Form\Form;
use Interop\Container\ContainerInterface;
use Auth\Form\Hydrator\UserPasswordHydrator;

class UserPassword extends Form
{
    /**
     * @var ContainerInterface
     */
    protected $forms;
    
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $hydrator = new UserPasswordHydrator();
            $this->setHydrator($hydrator);
        }
        return $this->hydrator;
    }
    
    public function init()
    {
        $this->setName('user-password-form');

        $this->add(
            array(
            'type' => 'hidden',
            'name' => 'id',
            )
        );
        
        $this->add(
            $this->forms->get('Auth/UserPasswordFieldset')
        );
        
        $this->add($this->forms->get('submitField'));
    }
    
    /**
     * @param ContainerInterface $forms
     * @return UserPassword
     */
    public static function factory(ContainerInterface $forms)
    {
        $form = new static();
        $form->forms = $forms->get('FormElementManager');
        
		return $form;
    }
}
