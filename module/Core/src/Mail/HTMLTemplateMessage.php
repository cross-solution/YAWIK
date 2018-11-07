<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Mail;

use Interop\Container\ContainerInterface;
use Zend\Mail\Header;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Model\ViewModel;
use Zend\Stdlib\Response;
use Zend\View\Variables as ViewVariables;
use Zend\Stdlib\ArrayUtils;

/**
 * Class HTMLTemplateMessage.
 * uses methods alike ViewModel
 * @package Core\Mail
 */
class HTMLTemplateMessage extends TranslatorAwareMessage
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * View variables
     * @var array|\ArrayAccess|\Traversable
     */
    protected $variables = array();

    protected $renderedBody;
    
    /**
     * HTMLTemplateMessage constructor.
     *
     * @param ContainerInterface $serviceManager
     * @param array $options
     */
    public function __construct(
        ContainerInterface $serviceManager,
        array $options = array()
    ) {
        // @TODO make this multipart
        parent::__construct($options);
        $this->serviceManager = $serviceManager;
        $this->getHeaders()->addHeader(Header\ContentType::fromString('Content-Type: text/html; charset=UTF-8'));
        $this->setEncoding('UTF-8');
        $this->variables = new ViewVariables();
    }

    /**
     * Property overloading: set variable value
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setVariable($name, $value);
    }

    /**
     * Property overloading: get variable value
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        $variables = $this->getVariables();
        return $variables[$name];
    }

    /**
     * Property overloading: do we have the requested variable value?
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $variables = $this->getVariables();
        return isset($variables[$name]);
    }

    /**
     * Property overloading: unset the requested variable
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if ($this->__isset($name)) {
            unset($this->variables[$name]);
        }
    }

    /**
     * Get a single view variable
     *
     * @param  string       $name
     * @param  mixed|null   $default (optional) default value if the variable is not present.
     * @return mixed
     */
    public function getVariable($name, $default = null)
    {
        $name = (string) $name;
        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        return $default;
    }

    /**
     * Set view variable
     *
     * @param  string $name
     * @param  mixed $value
     * @return ViewModel
     */
    public function setVariable($name, $value)
    {
        $this->variables[(string) $name] = $value;
        return $this;
    }

    /**
     * Set view variables en masse
     *
     * Can be an array or a Traversable + ArrayAccess object.
     *
     * @param  array|\ArrayAccess|\Traversable $variables
     * @param  bool $overwrite Whether or not to overwrite the internal container with $variables
     * @throws \InvalidArgumentException
     * @return self
     */
    public function setVariables($variables, $overwrite = false)
    {
        if (!is_array($variables) && !$variables instanceof \Traversable) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s: expects an array, or Traversable argument; received "%s"',
                    __METHOD__,
                    (is_object($variables) ? get_class($variables) : gettype($variables))
                )
            );
        }

        if ($overwrite) {
            if (is_object($variables) && !$variables instanceof \ArrayAccess) {
                $variables = ArrayUtils::iteratorToArray($variables);
            }

            $this->variables = $variables;
            return $this;
        }

        foreach ($variables as $key => $value) {
            $this->setVariable($key, $value);
        }

        return $this;
    }

    /**
     * Get view variables
     *
     * @return array|\ArrayAccess|\Traversable
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Clear all variables
     *
     * Resets the internal variable container to an empty container.
     *
     * @return ViewModel
     */
    public function clearVariables()
    {
        $this->variables = new ViewVariables();
        return $this;
    }

    /**
     *
     * @param $template
     *
     * @return self
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @example /module/Jobs/src/Jobs/Listener/PortalListener.php
     * @return string
     * @throws \InvalidArgumentException the mail body must completely be provided by the template, any other attempt is a misconception that may leave the coder in an quagmire
     */
    public function renderBodyText($force = true, $forceLanguage = null)
    {
        if (!$this->renderedBody || $force) {
            $viewModel    = new ViewModel();
            $response     = new Response();
            $body         = parent::getBodyText();
            if (!empty($body)) {
                throw new \InvalidArgumentException('mail body shall come from Template.');
            }

            /* @var \Zend\Mvc\View\Http\ViewManager $viewManager */
            $viewManager  = $this->serviceManager->get('ViewManager');
            $resolver = $this->serviceManager->get('ViewResolver');

            /* @var \Zend\Mvc\MvcEvent $event */
            $event = $this->serviceManager->get('Application')->getMvcEvent();
            $lang = $forceLanguage ?: $event->getRouteMatch()->getParam('lang');


            if ($resolver->resolve($this->getTemplate() . '.' . $lang)) {
                $viewModel->setTemplate($this->getTemplate() . '.' . $lang);
            } else {
                $viewModel->setTemplate($this->getTemplate());
            }

            $view         = $viewManager->getView();

            $viewModel->setVariables($this->getVariables());
            $viewModel->setVariable('mail', $this);
            $view->setResponse($response);
            $view->render($viewModel);
            $body = $response->getContent();

            $this->renderedBody = $body;
        }
    }

    public function getBodyText()
    {
        $this->renderBodyText();
        return $this->renderedBody;
    }
    
    /**
     * @param ContainerInterface $container
     *
     * @return static
     */
    public static function factory(ContainerInterface $container)
    {
        return new static($container);
    }
}
