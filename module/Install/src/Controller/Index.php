<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2015 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Install\Controller;

use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

/**
 * ${CARET}
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo write test 
 */
class Index extends AbstractActionController
{
    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'preDispatch'), 100);
    }

    public function preDispatch(MvcEvent $event)
    {
        $this->layout()->setVariable('lang', $this->params('lang'));

        $p = $this->params()->fromQuery('p');
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

    public function indexAction()
    {
        $form     = $this->getForm();
        $prereqs  = $this->plugin('Install/Prerequisites')->check();

        
        return $this->createViewModel(array(
                                          'prerequisites' => $prereqs,
                                          'form' => $form,
                                      ));
    }
    
    public function prereqAction()
    {
        $prereqs = $this->plugin('Install/Prerequisites')->check();

        $model = $this->createViewModel(array('prerequisites' => $prereqs), true);
        $model->setTemplate('install/index/prerequisites.ajax.phtml');

        return $model;
    }

    public function installAction()
    {
        $form = $this->getForm();
        $form->setData($_POST);

        if (!$form->isValid()) {
            return $this->createJsonResponse(array(
                'ok' => false,
                'errors' => $form->getMessages(),
                                 ));
        }

        $data = $form->getData();

        $ok = $this->plugin('Install/ConfigCreator')->process($data['db_conn'], $data['username'], $data['password']);

        $model = $this->createViewModel(array('ok' => $ok), true);
        $model->setTemplate('install/index/install.ajax.phtml');

        return $model;
    }


    /**
     *
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

    protected function createViewModel(array $params, $terminal = false)
    {
        if (!isset($params['lang'])) {
            $params['lang'] = $this->params('lang');
        }


        $model = new ViewModel($params);
        $terminal && $model->setTerminal($terminal);

        return $model;
    }

    protected function createJsonResponse(array $variables)
    {
        $response = $this->getResponse();
        $json     = Json::encode($variables);
        $response->setContent($json);

        return $response;
    }
}