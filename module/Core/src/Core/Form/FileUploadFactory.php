<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/**  */
namespace Core\Form;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Hydrator\HydratorPluginManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Core\Entity\Hydrator\EntityHydrator;
use Core\Entity\Hydrator\Strategy\FileUploadStrategy;
use Auth\Entity\AnonymousUser;
use Core\Entity\Hydrator\FileCollectionUploadHydrator;
use Zend\Stdlib\AbstractOptions;

/**
 * Factory for creating file upload formular elements.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class FileUploadFactory implements FactoryInterface
{
    /**
     * Service name of the form element used.
     *
     * @var string
     */
    protected $fileElement = 'Core/FileUpload';

    /**
     * Name of the form element.
     *
     * @var string
     */
    protected $fileName = 'file';

    /**
     * Class name of the file entity to use.
     *
     * @var string
     */
    protected $fileEntityClass = '\Core\Form\FileEntity';

    /**
     * Should the factored element allow multiple files to be selected?
     *
     * @var bool
     */
    protected $multiple = false;

    /**
     * The config key in the FileUploadFactory configuration entry.
     * @var string
     */
    protected $configKey;

    /**
     * The configuration stored in the FileUploadFactory configuration entry under {@link $configKey}.
     *
     * @var array|null
     */
    protected $config;

    /**
     * Optional Module Options. eg. "Jobs/Options" or "Applications/Options"
     *
     * @var string|null
     */
    protected $options;
	
	public function __invoke( ContainerInterface $container, $requestedName, array $options = null ) {
		/* @var $formElementManager \Zend\Form\FormElementManager\FormElementManagerV3Polyfill */
		$formElementManager = $container->get('FormElementManager');
		$options=null;
		if ($this->options) {
			$options = $container->get($this->options);
		}
		
		// Retrieve configuration.
		if ($this->configKey) {
			$config   = $container->get('config');
			$config   = isset($config['form_elements_config']['file_upload_factories'][$this->configKey])
				? $config['form_elements_config']['file_upload_factories'][$this->configKey]
				: array();
			$this->config = $config;
		}
		
		
		$form = new Form();
		$formElementManager->injectFactory($formElementManager,$form);
		$form->add(
			array(
				'type' => $this->fileElement,
				'name' => $this->fileName,
				'options' => array(
					/* @TODO: [ZF3] set this value to false will make upload field invisible in organization and user profile profile */
					'use_formrow_helper' => true,
				),
				'attributes' => array(
					'class' => 'hide',
				),
			)
		);
		/* @var $element \Core\Form\Element\FileUpload */
		$element = $form->get($this->fileName);
		$element->setIsMultiple($this->multiple);
		
		$user = $container->get('AuthenticationService')->getUser();
		
		if (isset($this->config['hydrator']) && $this->config['hydrator']) {
			/** @noinspection PhpUndefinedVariableInspection */
			$hydrator = $container->get('HydratorManager')->get($this->config['hydrator']);
		} else {
			/* @var $fileEntity \Core\Entity\FileInterface */
			$fileEntity = new $this->fileEntityClass();
			if ($user instanceof AnonymousUser) {
				$fileEntity->getPermissions()->grant($user, 'all');
			} else {
				$fileEntity->setUser($user);
			}
			
			$strategy = new FileUploadStrategy($fileEntity);
			if ($this->multiple) {
				$hydrator = new FileCollectionUploadHydrator($this->fileName, $strategy);
				$form->add(
					array(
						'type' => 'button',
						'name' => 'remove',
						'options' => array(
							'label' => /*@translate*/ 'Remove all',
						),
						'attributes' => array(
							'class' => 'fu-remove-all btn btn-danger btn-xs pull-right'
						),
					)
				);
			} else {
				$hydrator = new EntityHydrator();
				$hydrator->addStrategy($this->fileName, $strategy);
			}
		}
		
		$form->setHydrator($hydrator);
		$form->setOptions(array('use_files_array' => true));
		
		$this->configureForm($form, $options);
		return $form;
	}
	
	
	/**
     * The configuration from the FileUploadFactory configuration
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return Form
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    	return $this($serviceLocator,static::class);
    }

    /**
     * Configures the factored form.
     *
     * @param \Core\Form\Form $form
     * @param AbstractOptions $options
     */
    protected function configureForm($form, AbstractOptions $options)
    {
    }
}
