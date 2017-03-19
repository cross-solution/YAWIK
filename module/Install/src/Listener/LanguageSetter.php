<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */

/** */
namespace Install\Listener;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

/**
 * Detects user language based on query parameter and/or browser settings.
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @todo   write test
 * @since  0,20
 */
class LanguageSetter implements ListenerAggregateInterface
{

    /**
     * Attached callback handlers.
     *
     * @var CallbackHandler[]
     */
    protected $listeners;

    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), 1);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onRoute'), 1);
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    /**
     * Listens to the route event.
     *
     * Detects the language to use and sets translator locale.
     * The language is detected either via query parameter "lang" or
     * browser setting (ACCEPT-LANGUAGE header)
     *
     * @param MvcEvent $e
     */
    public function onRoute(MvcEvent $e)
    {
        /* @var $request \Zend\Http\PhpEnvironment\Request */
        $request = $e->getRequest();

        /* Detect language */
        $lang = $request->getQuery('lang');

        if (!$lang) {
            $headers = $request->getHeaders();
            if ($headers->has('Accept-Language')) {
                /* @var $acceptLangs \Zend\Http\Header\AcceptLanguage */
                $acceptLangs = $headers->get('Accept-Language');
                $locales = $acceptLangs->getPrioritized();
                $locale  = $locales[0];
                $lang    = $locale->type;
            } else {
                $lang = 'en';
            }
        }

        /* Set locale */
        $translator = $e->getApplication()->getServiceManager()->get('mvctranslator');
        $locale     = $lang . '_' . strtoupper($lang);

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
        \Locale::setDefault($locale);
        $translator->setLocale($locale);
        $routeMatch = $e->getRouteMatch();
        if ($routeMatch && $routeMatch->getParam('lang') === null) {
            $routeMatch->setParam('lang', $lang);
        }

        /* @var $router \Zend\Mvc\Router\SimpleRouteStack */
        $router = $e->getRouter();
        $router->setDefaultParam('lang', $lang);
    }
}
