<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
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
 *      
 *      // render string using explicit type methods
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
        if (true === $content) {
            return $this->start($type);
        }
        
        $markup = <<<MARKUP
    <div class="alert alert-$type alert-dismissable">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        $content
    </div>
MARKUP;
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
     * @param string|null $type
     * @param string $content|null
     * @return \Core\View\Helper\Alert|string|null
     * @uses render()
     */
    public function __invoke($type = null, $content = true)
    {
        if (null === $type) {
            return $this;
        }
        
        return $this->render($type, $content);
    }
}

