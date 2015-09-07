<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Controller;

use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
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
        $form    = $this->getForm();
        $prereqs = $this->plugin('Install/Prerequisites')->check();


        return $this->createViewModel(
            array(
                                          'prerequisites' => $prereqs,
                                          'form'          => $form,
                                      )
        );
    }

    /**
     * Action to check prerequisites via ajax request.
     *
     * @return ViewModel
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
     * @return Response|ViewModel
     */
    public function installAction()
    {
        $form = $this->getForm();
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

        $userOk = $this->plugin('Install/UserCreator')->process($data['db_conn'], $data['username'], $data['password']);
        $ok = $this->plugin('Install/ConfigCreator')->process($data['db_conn'], $data['email']);

        /*
         * Make sure there's no cached config files
         */
        $classmapCacheFile = 'cache/module-classmap-cache.module_map.php';
        file_exists($classmapCacheFile) && @unlink($classmapCacheFile);

        $configCacheFile   = 'cache/module-config-cache.production.php';
        file_exists($configCacheFile) && @unlink($configCacheFile);

        $model = $this->createViewModel(array('ok' => $ok), true);
        $model->setTemplate('install/index/install.ajax.phtml');

        return $model;
    }

    /**
     * Attachs default listeners to the event manager.
     */
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 100);
    }

    /**
     * Gets the installation form
     *
     * @return \Install\Form\Installation
     */
    protected function getForm()
    {
        $services = $this->getServiceLocator();
        $forms    = $services->get('FormElementManager');
        $form     = $forms->get('Install/Installation');

        return $form;
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
