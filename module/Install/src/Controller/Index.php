<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Controller;

use Core\Service\ClearCacheService;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill as FormElementManager;
use Zend\Json\Json;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\ResponseInterface;
use Zend\View\Model\ViewModel;

/**
 * Install module main controller.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 * @since  0.20
 */
class Index extends AbstractActionController
{
    protected $installForm;

    /**
     * @var ClearCacheService
     */
    private $cacheService;

    public function __construct(FormElementManager $formElementManager)
    {
        $this->installForm = $formElementManager->get('Install/Installation');

        $config = $formElementManager->getServiceLocator()->get('ApplicationConfig');
        $options = new ListenerOptions($config['module_listener_options']);
        $this->cacheService = new ClearCacheService($options);
    }
    
    /**
     * Hook for custom preDispatch event.
     *
     * @param MvcEvent $event
     */
    public function preDispatch(MvcEvent $event)
    {
        $this->layout()->setVariable('lang', $this->params('lang'));

        $p       = $this->params()->fromQuery('p');
        $request = $this->getRequest();

        if ($p && $request->isXmlHttpRequest()) {
            $routeMatch = $event->getRouteMatch();
            $routeMatch->setParam('action', $p);
            $response = $this->getResponse();
            $response->getHeaders()
                     ->addHeaderLine('Content-Type', 'application/json')
                     ->addHeaderLine('Content-Encoding', 'utf8');
        }
    }

    /**
     * Index action.
     *
     * @return ViewModel
     */
    public function indexAction()
    {
        // Clear the user identity, if any. (#370)
        if (PHP_SESSION_ACTIVE !== session_status()) {
            session_start();
        }
        session_destroy();

        $form    = $this->installForm;
        $prereqs = $this->plugin('Install/Prerequisites')->check();
    
        return $this->createViewModel(
            array(
                'prerequisites' => $prereqs,
                'form'          => $form,
                'lang'          => $this->params('lang'),
            )
        );
    }

    /**
     * Action to check prerequisites via ajax request.
     *
     * @return ViewModel|ResponseInterface
     */
    public function prereqAction()
    {
        $prereqs = $this->plugin('Install/Prerequisites')->check();

        $model = $this->createViewModel(array('prerequisites' => $prereqs), true);
        $model->setTemplate('install/index/prerequisites.ajax.phtml');

        return $model;
    }

    /**
     * Main working action. Creates the configuration.
     *
     * @return ResponseInterface|ViewModel
     */
    public function installAction()
    {
        $form = $this->installForm;
        $form->setData($_POST);

        if (!$form->isValid()) {
            return $this->createJsonResponse(
                array(
                    'ok'     => false,
                    'errors' => $form->getMessages(),
                )
            );
        }

        $data = $form->getData();

        try {
            $options = [
                'connection' => $data['db_conn'],
            ];
            $userOk = $this->plugin('Install/UserCreator', $options)->process($data['username'], $data['password'], $data['email']);
            $ok = $this->plugin('Install/ConfigCreator')->process($data['db_conn'], $data['email']);
        } catch (\Exception $exception) {
            /* @TODO: provide a way to handle global error message */
            return $this->createJsonResponse([
                'ok'        => false,
                'errors'    => [
                    'global' => [$exception->getMessage()]
                ]
            ]);
        }

        /*
         * Make sure there's no cached config files
         */
        $this->cacheService->clearCache();

        $model = $this->createViewModel(array('ok' => $ok), true);
        $model->setTemplate('install/index/install.ajax.phtml');

        return $model;
    }

    /**
     * Attaches default listeners to the event manager.
     */
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();
    
        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array( $this, 'preDispatch' ), 100);
    }

    /**
     * Creates a view model
     *
     * @param array $params
     * @param bool  $terminal
     *
     * @return ViewModel
     */
    protected function createViewModel(array $params, $terminal = false)
    {
        if (!isset($params['lang'])) {
            $params['lang'] = $this->params('lang');
        }


        $model = new ViewModel($params);
        $terminal && $model->setTerminal($terminal);

        return $model;
    }

    /**
     * Create a json response object for ajax requests.
     *
     * @param array $variables
     *
     * @return \Zend\Stdlib\ResponseInterface
     */
    protected function createJsonResponse(array $variables)
    {
        $response = $this->getResponse();
        $json     = Json::encode($variables);
        $response->setContent($json);

        return $response;
    }
}
