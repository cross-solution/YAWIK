<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core view helper */ 
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * View helper to render Twitter Bootsrtap alert messages.
 * 
 * Content to this helper can be provided as string or with its
 * content capture mechanism:
 * 
 * <code>
 * 
 *      // render string using __invoke 
 *      echo $this->alert('info', 'This is an info message');
 *      // if you want to additional parameter like classes or id, you can put them into an array
 *      // there are several options available
 *      // (1) you can provide the type in an array along with the parameter
 *      echo $this->alert(array($type => 'info'. 'par1' => '...'), 'This is an info message');
 *      // (2) you can put everything in an array
 *      echo $this->alert(array($type => 'info', 'content' => 'This is an info message', 'par1' => '...'));
 *      // (3) you can put the content into an array
 *      echo $this->alert('info', array('content' => 'This is an info message', 'par1' => '...'));
 *      // (4) you can use a subsequent method (recommended)
 *      echo $this->alert(array('par1' => '...'))->warning('content');
 *      echo $this->alert()->info('This is an info message');
 *      echo $this->alert()->danger('This is an error message');
 *      
 *      // render alert box using content capture
 *      $this->alert()->start('info');
 *      ?> Message HTML whatever.. <?php
 *      echo $this->alert()->end();
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
 *      
 * </code>
 * 
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @author Mathias Gelhausen <weitz@cross-solution.de>
 */
class Alert extends AbstractHelper
{
    
    /**#@+
     * Type of alert boxes.
     * @var string
     */
    const TYPE_INFO = 'info';
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
    protected $captureType = self::TYPE_INFO;


    protected $options=array();
    
    /**
     * Starts content capturing.
     * 
     * @param string $type
     * @throws \RuntimeException if there's already another capturing process running.
     */
    public function start($type = self::TYPE_INFO)
    {
        if ($this->captureLock) {
            throw new \RuntimeException('Cannot start capture, there is already a capture running.');
        }
        $this->captureLock = true;
        $this->captureType = $type;
        ob_start();
    }
    
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
        $this->captureLock = false;
        $content = ob_get_clean();
        return $this->render($this->captureType, $content);
    }
    
    /**
     * Renders an alert box.
     * 
     * if <i>TRUE</i> is passed with <b>$content</b>, a capture process is started.
     * 
     * @param string $type
     * @param string|true $content
     * @return string|null
     * @uses start()
     */
    public function render($type, $content)
    {
        if (is_array($content)) {
            $options = $content;
        }
        else {
            $options = $this->options;
        }
        if (!empty($options)) {
            if (array_key_exists ('content', $options)) {
                $content = $options['content'];
                unset($options['content']);
            }
        }
        if (true === $content) {
            if (!empty($options)) {
                throw new \InvalidArgumentException('alert with content-value true does not take additional arguments');
            }
            return $this->start($type);
        }

        $classes = array('alert', 'alert-' . $type, 'alert-dismissable');
        $id = '';
        // additional arguments
        if (is_array($options)) {
            if (array_key_exists('classes', $options)) {
                $classes = array_merge($classes, (array) $options['classes']);
                unset($options['classes']);
            }
            if (array_key_exists('id', $options)) {
                $id = 'id="' . $options['id'] . '"';
                unset($options['id']);
            }
        }

        $classAttr = implode (' ', $classes);

        $markup = <<<MARKUP
    <div $id class="$classAttr">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        $content
    </div>
MARKUP;
        $this->options = array();
        return $markup;
    }
    
    /**
     * Shortcut for rendering an info alert.
     * 
     * If <b>$content</b> is <i>TRUE</i> or not passed, a capture process is started.
     * 
     * @param string $content
     * @return string
     * @uses render()
     */
    public function info($content = true)
    {
        return $this->render(self::TYPE_INFO, $content);
    }
    
    /**
     * Shortcut for rendering a success alert.
     *
     * If <b>$content</b> is <i>TRUE</i> or not passed, a capture process is started.
     *
     * @param string $content
     * @return string
     * @uses render()
     */
    public function success($content = true)
    {
        return $this->render(self::TYPE_SUCCESS, $content);
    }
    
    /**
     * Shortcut for rendering a warning alert.
     *
     * If <b>$content</b> is <i>TRUE</i> or not passed, a capture process is started.
     *
     * @param string $content
     * @return string
     * @uses render()
     */
    public function warning($content = true)
    {
        return $this->render(self::TYPE_WARNING, $content);
    }
    
    /**
     * Shortcut for rendering a danger alert.
     *
     * If <b>$content</b> is <i>TRUE</i> or not passed, a capture process is started.
     *
     * @param string $content
     * @return string
     * @uses render()
     */
    public function danger($content = true)
    {
        return $this->render(self::TYPE_DANGER, $content);
    }
    
    /**
     * Entry point, if object is called as function.
     * 
     * if <b>$type</b> is null, return this instance.
     * if <b>$content</b> is true or not passed, starts a capture process.
     * 
     * @param string|array|null $type
     * @param string $content|array|null
     * @return \Core\View\Helper\Alert|string|null
     * @uses render()
     */
    public function __invoke($type = null, $content = null)
    {
        if (null === $type) {
            return $this;
        }
        if (is_array($type)) {
            $options = $type;
            $type = null;
            if (array_key_exists('content', $options)) {
                if (!isset ($content)) {
                    $content = $options['content'];
                    unset ($options['content']);
                }
                else {
                    throw new \InvalidArgumentException('alert content was already given by parameter, overwriting is not intended');
                }
            }
            if (array_key_exists('type', $options)) {
                $type = $options['type'];
                unset ($options['type']);
                if (!empty($options)) {
                    if (isset($content) && !empty($options)) {
                        $options['content'] = $content;
                        $content = $options;
                    }
                }
            }
            $this->options = $options;
        }
        if (!isset($type)) {
            if (isset($content)) {
                // on the rather rare occassion, that we already have provided the content, but not how to display it
                $this->options['content'] = $content;
            }
            return $this;
        }
        if (!isset($content)) {
            $content = true;
        }
        
        return $this->render($type, $content);
    }
}

