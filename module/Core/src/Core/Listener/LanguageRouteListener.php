<?php


namespace Core\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\Mvc\Router\RouteMatch;
use Locale;

class LanguageRouteListener implements ListenerAggregateInterface
{

    
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    protected $defaultLanguage;
    
    protected $availableLanguages = array(
            'en' => 'en_EN',
            'de' => 'de_DE',
            'fr' => 'fr_FR'
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
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), $priority);
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
        if (0 !== strpos($routeMatch->getMatchedRouteName(), 'lang')) {
            // We do not have a language enabled route here.
            // but we need to provide a language to the navigation container
            $lang = $this->detectLanguage($e);
            $this->setTranslatorLocale($e, $lang);
            $this->setNavigationParams($e, $lang);
            return;
        }
        $language = $routeMatch->getParam('lang', '__NOT_SET__');
        if ($this->isAvailableLanguage($language)) {
            $this->setTranslatorLocale($e, $language);
            $this->setNavigationParams($e, $language);
        } else {
            $e->setError(Application::ERROR_ROUTER_NO_MATCH);
            $e->setTarget($this);
            $result = $e->getApplication()->getEventManager()->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
            
            return $result->last();
        }
    }

    public function onDispatchError(MvcEvent $e)
    {
        if ($e->getRequest() instanceOf \Zend\Console\Request 
            || Application::ERROR_ROUTER_NO_MATCH != $e->getError()
        ) {
            return;
        } 
        
        $router = $e->getRouter();
        $basePath=$router->getBaseUrl();
        
        
        if (preg_match('~^' . $basePath . '/([a-z]{2})(?:/|$)~', $e->getRequest()->getRequestUri(), $match)) {
            /* It seems we have already a language in the URI
             * Now there are two possibilities:
             * 
             * 1: The Language is not supported 
             *    -> set translator locale to browser locale if supported
             *       or default. Do not forget to set the appropriate route param 'lang'
             *    
             * 2: Language is supported, but the rest of the route
             *    does not match
             *    -> set translator locale to provided language
             */
            
            $lang = array_key_exists($match[1], $this->availableLanguages)
                  ? $match[1]
                  : $this->detectLanguage($e);
                
            $this->setNavigationParams($e, $lang);
            $this->setTranslatorLocale($e, $lang);
            return;
        }
        
        /* We have no language key in the URI
         * Let's prepend the browser language locale if supported or 
         * the default to the URI.
         * 
         * If a route matches this prepended URI, we do a redirect,
         * else we set the translator locale and let the event propagate
         * to the ROUTE_NO_MATCH error renderer.
         */
        $request = clone $e->getRequest(); // clone the request, because maybe we
        $origUri = str_replace($basePath, '', $request->getRequestUri());
        $lang = $this->detectLanguage($e);
        $langUri = rtrim("$basePath/$lang$origUri", '/');        
        if ($router->match($request->setUri($langUri)) instanceOf RouteMatch) {
            $e->stopPropagation(true);
            //$e->setError(false);
            return $this->redirect($e->getResponse(), $langUri);
        }

        $this->setNavigationParams($e, $lang);
        $this->setTranslatorLocale($e, $lang);
    }

    public function getDefaultLanguage()
    {
        if (!$this->defaultLanguage) {
            $availableLanguages = array_keys($this->availableLanguages);
            $this->defaultLanguage = array_shift($availableLanguages);
        }
        return $this->defaultLanguage;
    }

    protected function isAvailableLanguage($lang)
    {
        return array_key_exists($lang, $this->availableLanguages);
    }

    protected function detectLanguage(MvcEvent $e)
    {
        $auth = $e->getApplication()->getServiceManager()->get('AuthenticationService');
        if ($auth->hasIdentity()) {
            $user = $auth->getUser();
            $settings = $user->getSettings('Core');
            if ($lang = $settings->language) {
                return $lang;
            }
        }

        $headers = $e->getRequest()->getHeaders();
        if ($headers->has('Accept-Language')) {
            $locales = $headers->get('Accept-Language')->getPrioritized();
            $localeFound=false;
            foreach ($locales as $locale) {
                if (array_key_exists($locale->type, $this->availableLanguages)) {
                    $lang = $locale->type;
                    $localeFound = true;
                    break;
                }
            }
            if (!$localeFound) {
                $lang = $this->getDefaultLanguage();
            }
        } else {
            $lang = $this->getDefaultLanguage();
        }
        
        return $lang;
    }
    
    protected function redirect($response, $uri)
    {
        $response->setStatusCode(302);
        $response->getHeaders()->addHeaderline('Location', $uri);
        return $response;
    }
    
    protected function setTranslatorLocale(MvcEvent $e, $lang)
    {
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $locale = $this->availableLanguages[$lang];
        
        setlocale(LC_ALL, array(
            $locale . ".utf8",
            $locale . ".iso88591",
            $locale,
            substr($locale, 0, 2),
            'de_DE.utf8',
            'de_DE',
            'de'
        ));
        Locale::setDefault($locale);
        $translator->setLocale($locale);
        
        if ($match = $e->getRouteMatch()) {
            $match->setParam('lang', $lang);
        } else {
            $e->setParam('lang', $lang);
        }
    }
    
    protected function setNavigationParams(MvcEvent $e, $lang)
    {
        $nav = $e->getApplication()->getServiceManager()->get('main_navigation');
        $this->setRecursiveNavigationParams($nav->getPages(), $lang);
    }
    
    protected function setRecursiveNavigationParams($pages, $lang)
    {
        foreach ($pages as $page) {
            if ($page instanceof \Zend\Navigation\Page\Mvc) {
                $route = $page->getRoute();
                if (0 === strpos($route, 'lang')) {
            
                    $params = $page->getParams();
                    if (!isset($params['lang']) || $lang != $params['lang']) {
                        $params['lang'] = $lang;
                        $page->setParams($params);
                    }
                }
            }
            $this->setRecursiveNavigationParams($page->getPages(), $lang);
        }
    }
}
