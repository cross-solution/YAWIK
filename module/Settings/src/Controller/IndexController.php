<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author Mathias Weitz <weitz@cross-solution.de>
 * @author Carsten Bleek <bleek@cross-solution.de>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */

/** Settings controller */
namespace Settings\Controller;

use Interop\Container\ContainerInterface;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\EventManager\Event;
use Zend\Http\PhpEnvironment\Response;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;

/**
 * Main Action Controller for Settings module
 *
 */
class IndexController extends AbstractActionController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * @var FormElementManager
     */
    private $formManager;
    
    private $viewHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        FormElementManager $formManager,
        $viewHelper
    ) {
        $this->translator = $translator;
        $this->formManager = $formManager;
        $this->viewHelper = $viewHelper;
    }
    
    public static function factory(ContainerInterface $container)
    {
        $translator = $container->get('translator');
        return new self(
            $translator,
            $container->get('FormElementManager'),
            $container->get('ViewHelperManager')
        );
    }
    
    public function indexAction()
    {
        $translator = $this->translator;
        $moduleName = $this->params('module', 'Core');
        
        
        try {
            $settings = $this->settings($moduleName);
        } catch (\InvalidArgumentException $e) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);
            return [
                'message' => sprintf($translator->translate('Settings "%s" does not exists'), $moduleName),
                'exception' => $e
            ];
        }
        
        $jsonFormat = 'json' == $this->params()->fromQuery('format');
        if (!$this->getRequest()->isPost() && $jsonFormat) {
            return $settings->toArray();
        }
        
        $mvcEvent = $this->getEvent();
        $mvcEvent->setParam('__settings_active_module', $moduleName);
        
        $formManager = $this->formManager;
        $formName = $moduleName . '/SettingsForm';
        if (!$formManager->has($formName)) {
            $formName = "Settings/Form";
        }
        
        // Fetching an distinct Settings
        
        
        // Write-Access is per default only granted to the own module - change that
        $settings->enableWriteAccess();
        
        $form = $formManager->get($formName);
        
        $vars = array();
        $vars['form'] = $form;
        // Binding the Entity to the Formular
        $form->bind($settings);
        $data = $this->getRequest()->getPost();
        if (0 < count($data)) {
            $form->setData($data);
            $valid   = $form->isValid();
            $partial = $this->viewHelper->get('partial');
            $text    = $valid
                     ?  /*@translate*/'Changes successfully saved'
                     :  /*@translate*/'Changes could not be saved';
            $this->notification()->success($translator->translate($text));

            if ($valid) {
                $event = new Event(
                    'SETTINGS_CHANGED',
                    $this,
                    array('settings' => $settings)
                );
                $this->getEventManager()->trigger($event->getName(), $event);
                $vars['valid'] = true;
            } else {
                $vars['error'] = $form->getMessages();
            }
            return new JsonModel($vars);
        }

        
        return $vars;
    }
}
