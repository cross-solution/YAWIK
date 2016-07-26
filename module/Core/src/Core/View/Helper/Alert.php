<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license       MIT
 */

/** Core view helper */
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper to render Twitter Bootsrtap alert messages.
 * Content to this helper can be provided as string or with its
 * content capture mechanism:
 * <code>
 *      // render string using __invoke
 *      echo $this->alert('info', 'This is an info message');
 *
 *      // pass options
 *      echo $this->alert('info', 'This is an info message', array('id' => 'my_alert'));
 *
 *      // render string using explicit type methods
 *      echo $this->alert()->info('This is an info message');
 *      echo $this->alert()->danger('This is an error message');
 *
 *      echo $this->alert()->success('This is a success message', array('class' => 'my_custom_class'));
 *
 *      // render alert box using content capture
 *      $this->alert()->start('info');
 *      ?> Message HTML whatever.. <?php
 *      echo $this->alert()->end();
 *
 *      echo $this->alert()->start('info', array('dismissable' => true));
 *      // or w/o type (defaults to info)
 *      echo $this->alert()->start(array('dismissable'=> true));
 *
 *      // render alert using content capture with expicit type methods
 *      $this->alert()->warning(); // You may pass in TRUE as argument
 *      ?> <p>You are about to <strong>render</strong> an alert box</p> <?php
 *      echo $this->alert()->end();
 *
 *      // render alert using content capture with __invoke
 *      $this->alert('danger'); ?>
 *      <p>Conetn of alert box </p>
 *      <?php echo $this->alert()->end();
 * </code>
 * @method info()
 * @method warning()
 * @method success()
 * @method danger()

 *
*@author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class Alert extends AbstractHelper
{

    /**#@+
     * Type of alert boxes.
     *
     * @var string
     */
    const TYPE_INFO    = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER  = 'danger';
    /**#@-*/

    /**
     * Flag indicating, wether a capture is running at the moment.
     *
     * @var boolean
     */
    protected $captureLock = false;

    /**
     * The type of the alert box which is currently captured.
     *
     * @var string
     */
    protected $captureType;

    protected $captureOptions;

    /**
     * Ends content capturing and returns the captured content.
     *
     * @throws \RuntimeException if there's no capturing process running
     * @return string
     * @uses render()
     */
    public function end()
    {
        if (!$this->captureLock) {
            throw new \RuntimeException('Cannot end capture, there is no capture running.');
        }

        $type                 = $this->captureType;
        $content              = ob_get_clean();
        $options              = $this->captureOptions;
        $this->captureLock    = false;
        $this->captureType    = null;
        $this->captureOptions = null;

        return $this->render($type, $content, $options);
    }

    /**
     * Renders an alert box.
     *
     * if <i>TRUE</i> is passed with <b>$content</b>, a capture process is started.
     * if <b>$type</b> is null, return this instance.
     * if <b>$content</b> is true or not passed, starts a capture process.
     * Following options are recognized:
     *      - "id":           sets an id to the alert div
     *      - "class":        String appended to the class-Attribute
     *      - "dismissable":  Boolean value wether the alert will be dismissable or not.
     *
     * @param array|string|null $type
     * @param array|string|bool $content
     * @param array             $options Additional options array
     *
     * @return string|null
     * @uses start()
     */
    public function render($type = null, $content = true, array $options = array())
    {
        if (is_array($type)) {
            $options = $type;
            $type    = self::TYPE_INFO;
            $content = true;
        } elseif (is_array($content)) {
            $options = $content;
            $content = true;
        }

        if (true === $content) {
            return $this->start($type, $options);
        }

        $id    = isset($options['id']) ? ' id="' . $options['id'] . '"' : '';
        $class = isset($options['class']) ? ' ' . $options['class'] : '';
        if ((isset($options['dismissable']) && $options['dismissable'])
            || !isset($options['dismissable'])
        ) {
            $class .= ' alert-dismissable';
            $content = '<button type="button" class="close" data-dismiss="alert">&times;</button>'
                     . '<span class="notification-content">' . $content . '</span>';
        }

        $target = array_key_exists('target', $options)?' target="' . $options['target'] . '"':'';
        $markup = '<div ' . $id . ' class="alert alert-' . $type . $class . '" ' . $target . '>' . $content . '</div>' . PHP_EOL;
        return $markup;
    }

    /**
     * Starts content capturing.
     *
     * @param string|array $type
     * @param array        $options see {@łink render()} for information.
     *
     * @return self
     * @throws \RuntimeException if there's already another capturing process running.
     */
    public function start($type = self::TYPE_INFO, array $options = array())
    {
        if ($this->captureLock) {
            throw new \RuntimeException('Cannot start capture, there is already a capture running.');
        }
        $this->captureLock    = true;
        $this->captureType    = $type;
        $this->captureOptions = $options;
        ob_start();

        return $this;
    }

    /**
     * Implement convinient functions for alert types.
     *
     * @param string $method
     * @param array  $args
     *
     * @return self|string
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (!in_array($method, array(self::TYPE_INFO, self::TYPE_DANGER, self::TYPE_SUCCESS, self::TYPE_WARNING))) {
            throw new \BadMethodCallException('Unknown method: ' . $method);
        }

        // We know that $method is one of the valid types, so we can safely use it as "type"-parameter for the
        // render method.
        array_unshift($args, $method);

        return call_user_func_array(array($this, 'render'), $args);
    }

    /**
     * Entry point, if object is called as function.
     * if <b>$type</b> is null, return this instance.
     * else proxies to {@link render()}.
     *
     * @param array|string|null $type
     * @param array|string|bool $content
     * @param array             $options Additional options array
     *
     * @return \Core\View\Helper\Alert|string
     * @uses render()
     */
    public function __invoke($type = null, $content = true, array $options = array())
    {
        if (null === $type) {
            return $this;
        }

        return $this->render($type, $content, $options);
    }
}
