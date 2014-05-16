<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013-2104 Cross Solution (http://cross-solution.de)
 * @license   AGPLv3
 */

/** Alert.php */ 
namespace Core\View\Helper;

use Zend\View\Helper\AbstractHelper;

class Alert extends AbstractHelper
{
    
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER  = 'danger';
    
    protected $captureLock = false;
    protected $captureType = self::TYPE_INFO;
    
    public function start($type = self::TYPE_INFO)
    {
        if ($this->captureLock) {
            throw new \RuntimeException('Cannot start capture, there is already a capture running.');
        }
        $this->captureLock = true;
        $this->captureType = $type;
        ob_start();
    }
    
    public function end()
    {
        if (!$this->captureLock) {
            throw new \RuntimeException('Cannot end capture, there is no capture running.');
        }
        $this->captureLock = false;
        $content = ob_get_clean();
        return $this->render($this->captureType, $content);
    }
    
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
    
    public function info($content = true)
    {
        return $this->render(self::TYPE_INFO, $content);
    }
    
    public function success($content = true)
    {
        return $this->render(self::TYPE_SUCCESS, $content);
    }
    
    public function warning($content = true)
    {
        return $this->render(self::TYPE_WARNING, $content);
    }
    
    public function danger($content = true)
    {
        return $this->render(self::TYPE_DANGER, $content);
    }
    
    public function __invoke($type = null, $content = true)
    {
        if (null === $type) {
            return $this;
        }
        
        return $this->render($type, $content);
    }
}

