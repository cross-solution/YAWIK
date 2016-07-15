<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** ActionController of Core */
namespace Cv\Controller;

use Cv\Form\SearchForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;

/**
 * Main Action Controller for the application.
 * Responsible for displaying the home site.
 *
 */
class IndexController extends AbstractActionController
{
    /**
     * @var SearchForm
     */
    protected $searchForm;

    public function __construct(SearchForm $searchForm)
    {
        $this->searchForm = $searchForm;
    }

    /**
     * Home site
     *
     */
    public function indexAction()
    {
        /* @var \Zend\Http\Request $request */
        $request = $this->getRequest();
        $params = $request->getQuery();
        $jsonFormat = 'json' == $request->getQuery()->get('format');
        $event = $this->getEvent();
        $routeMatch = $event->getRouteMatch();
        $matchedRouteName = $routeMatch->getMatchedRouteName();
        $url = $this->url()->fromRoute($matchedRouteName, array(), array('force_canonical' => true));


        if (!$jsonFormat && !$request->isXmlHttpRequest()) {
            $session = new Session('Cv\Index');
            $sessionKey = $this->auth()->isLoggedIn() ? 'userParams' : 'guestParams';
            $sessionParams = $session[$sessionKey];
            if ($sessionParams) {
                foreach ($sessionParams as $key => $value) {
                    $params->set($key, $params->get($key, $value));
                }
            }
            $session[$sessionKey] = $params->toArray();

            $this->searchForm->bind($params);
        }

        $params = $params->get('params', []);

        if (isset($params['l']['data']) &&
            isset($params['l']['name']) &&
            !empty($params['l']['name'])
        ) {
            /* @var \Geo\Form\GeoText $geoText */
            $geoText = $this->searchForm->get('params')->get('l');

            $geoText->setValue($params['l']);
            $params['location'] = $geoText->getValue('entity');
        }


        $this->searchForm->setAttribute('action', $url);
        $paginator = $this->paginator('Cv/Paginator', $params);

        $options = $this->searchForm->getOptions();
        $options['showButtons'] = false;
        $this->searchForm->setOptions($options);

        $return = array(
            'resumes' => $paginator,
            'filterForm' => $this->searchForm
        );
        $model = new ViewModel($return);

        return $model;
    }

    public function viewAction()
    {

    }
}
