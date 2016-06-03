<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author fedys
 */

namespace Core\Form;

use Core\Entity\EntityInterface;

interface ContainerInterface
{

    /**
     * @param EntityInterface $entity
     * @return ContainerInterface
     */
    public function setEntity(EntityInterface $entity);
    
    /**
     * @param array $params
     * @return ContainerInterface
     */
    public function setParams(array $params);
    
    /**
     * @param string $key
     * @return \Zend\Form\FormInterface|ContainerInterface|null
     */
    public function getForm($key);
}
