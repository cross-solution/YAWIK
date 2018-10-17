<?php
/**
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license MIT
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 */
namespace Core\Listener;

use Core\Options\ModuleOptions;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Application;
use Zend\Router\Http\RouteMatch;
use Locale;
use Core\I18n\Locale as LocaleService;

/**
 * Class LanguageRouteListener
 *
 * @package Core\Listener
 * @authro Carsten Bleek <cbleek@yawik.org>
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @author Anthonius Munthi <me@itstoni.com>
 */
class LanguageRouteListener implements ListenerAggregateInterface
{

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @var LocaleService
     */
    protected $localeService;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;


    /**
     * LanguageRouteListener constructor.
     *
     * @param LocaleService $localeService
     * @param ModuleOptions $moduleOptions
     */
    public function __construct(LocaleService $localeService, ModuleOptions $moduleOptions)
    {
        $this->localeService = $localeService;
        $this->moduleOptions = $moduleOptions;
    }
        
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
            $this->setLocale($e, $lang);
            return;
        }
        $language = $routeMatch->getParam('lang', '__NOT_SET__');
        if ($this->localeService->isLanguageSupported($language)) {
            $this->setLocale($e, $language);
        } else {
            $e->setError(Application::ERROR_ROUTER_NO_MATCH);
            $e->setTarget($this);
            $eventManager = $e->getApplication()->getEventManager();
            $eventManager->setEventPrototype($e);
            $result = $eventManager->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
            
            return $result->last();
        }
    }

    public function onDispatchError(MvcEvent $e)
    {
        if ($e->getRequest() instanceof \Zend\Console\Request
            || Application::ERROR_ROUTER_NO_MATCH != $e->getError()
        ) {
            return;
        }
        
        $router = $e->getRouter();
        $basePath=$router->getBaseUrl();
        $match = [];
        
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
            
            $lang = $this->localeService->isLanguageSupported($match[1])
                  ? $match[1]
                  : $this->detectLanguage($e);
            
            $this->setLocale($e, $lang);
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

        $options = $this->moduleOptions;
        // using default language if detect language is disabled
        $lang = $options->isDetectLanguage() ? $this->detectLanguage($e):$options->getDefaultLanguage();
        $langUri = rtrim("$basePath/$lang$origUri", '/');
        if ($router->match($request->setUri($langUri)) instanceof RouteMatch) {
            $e->stopPropagation(true);
            //$e->setError(false);
            return $this->redirect($e->getResponse(), $langUri);
        }

        
        $this->setLocale($e, $lang);
    }

    /**
     * @param MvcEvent $e
     * @return string
     */
    protected function detectLanguage(MvcEvent $e)
    {
        $auth = $e->getApplication()
            ->getServiceManager()
            ->get('AuthenticationService');
        $user = $auth->hasIdentity() ? $auth->getUser() : null;
        
        return $this->localeService->detectLanguage($e->getRequest(), $user);
    }

    /**
     * @param $response
     * @param $uri
     *
     * @return mixed
     */
    protected function redirect($response, $uri)
    {
        $response->setStatusCode(302);
        $response->getHeaders()->addHeaderline('Location', $uri);
        return $response;
    }

    /**
     * @param MvcEvent $e
     * @param          $lang
     */
    protected function setLocale(MvcEvent $e, $lang)
    {
        $translator = $e->getApplication()->getServiceManager()->get('translator');
        $locale = $this->localeService->getLocaleByLanguage($lang);
        
        setlocale(
            LC_ALL,
            array(
            $locale . ".utf8",
            $locale . ".iso88591",
            $locale,
            substr($locale, 0, 2),
            'de_DE.utf8',
            'de_DE',
            'de'
            )
        );
        Locale::setDefault($locale);

        $translator->setLocale($locale);
        $translator->setFallbackLocale(substr($locale, 0, 2));
        $routeMatch = $e->getRouteMatch();
        if ($routeMatch && $routeMatch->getParam('lang') === null) {
            $routeMatch->setParam('lang', $lang);
        }
        $e->getRouter()->setDefaultParam('lang', $lang);
    }
}
