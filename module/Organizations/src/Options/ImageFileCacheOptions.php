<?php
/**
 * YAWIK
*
* @filesource
* @license    MIT
* @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
*/
namespace Organizations\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Image file cache options
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class ImageFileCacheOptions extends AbstractOptions
{

    /**
     * Flag whether cache is enabled
     *
     * @var bool
     */
    protected $enabled = true;
    
    /**
     * Path to the directory in a file system
     *
     * @var string
     */
    protected $filePath;
    
    /**
     * Path to the directory accessible via web server
     *
     * @var string
     */
    protected $uriPath = '/static/Organizations/Image';
    
    /**
     * @param array|Traversable|null $options
     */
    public function __construct($options = null)
    {
        // We are relative to the application dir (see public/index.php)
        $this->filePath = 'public' . $this->uriPath;
        
        parent::__construct($options);
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return ImageFileCacheOptions
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return ImageFileCacheOptions
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getUriPath()
    {
        return $this->uriPath;
    }

    /**
     * @param string $uriPath
     * @return ImageFileCacheOptions
     */
    public function setUriPath($uriPath)
    {
        $this->uriPath = $uriPath;
        return $this;
    }
}
