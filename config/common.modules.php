<?php
/**
 * YAWIK
 * Common modules configuration to be used
 * in development, production, or install
 *
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Anthonius Munthi <me@itstoni.com>
 * @since 0.29
 */

return array(
	'Zend\ServiceManager\Di',
	'Zend\Session',
	'Zend\Router',
	'Zend\Navigation',
	'Zend\I18n',
	'Zend\Filter',
	'Zend\InputFilter',
	'Zend\Form',
	'Zend\Validator',
	'Zend\Log',
	'Zend\Mvc\Plugin\Prg',
	'Zend\Mvc\Plugin\Identity',
	'Zend\Mvc\Plugin\FlashMessenger',
	'Zend\Mvc\I18n',
	'Zend\Mvc\Console',
	'Zend\Hydrator',
	'Zend\Serializer',
	'DoctrineModule',
	'DoctrineMongoODMModule',
);