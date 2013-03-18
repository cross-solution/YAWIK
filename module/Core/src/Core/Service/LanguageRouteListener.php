<?php


namespace Core\Service;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;

class LanguageRouteListener implements ListenerAggregateInterface
{

    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    
    protected $availableLanguages = array(
			'de'	=> 'de_DE',
            'at'    => 'de_AT',
			'en'	=> 'en_EN',
            'us'    => 'en_US',
	);
		
    /**
     * Attach to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  integer $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), $priority);
    }

    /**
     * Detach all our listeners from the event manager
     *
     * @param  EventManagerInterface $events
     * @return void
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listen to the "route" event
     * 
     * @param  MvcEvent $e
     * @return null
     */
    public function onRoute(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
		$language = $routeMatch->getParam('lang', '');
		
		
		$translator = $e->getApplication()->getServiceManager()->get('translator');
		
		
		if (array_key_exists($language, $this->availableLanguages)) {
		    $lang = $this->availableLanguages[$language];
			setlocale(LC_ALL, $lang);
			$translator->setLocale($lang);
		} else {
			$response = $e->getResponse();
			$response->setStatusCode(302);
			//redirect to default language...
			$response->getHeaders()->addHeaderLine('Location', '/en');
		}
        
    }
}
