<?php
/**
 * YAWIK
 *
 * @filesource
 * @license    MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
namespace Organizations\ImageFileCache;

use Organizations\Entity\OrganizationImage;
use Organizations\Options\ImageFileCacheOptions as Options;
use Zend\Stdlib\ErrorHandler;
use InvalidArgumentException;
use RuntimeException;

/**
 * Image file cache manager
 *
 * @author Miroslav Fedeleš <miroslav.fedeles@gmail.com>
 * @since 0.28
 */
class Manager
{
    
    /**
     * @var Options
     */
    protected $options;
    
    /**
     * @param Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Returns image URI
     *
     * @param OrganizationImage $image
     * @return string
     */
    public function getUri(OrganizationImage $image)
    {
        if ($this->options->getEnabled()) {
            return sprintf('%s/%s', $this->options->getUriPath(), $this->getImageSubPath($image));
        }
        
        return $image->getUri();
    }
    
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->options->getEnabled();
    }
    
    /**
     * Store an image as a file in a file system
     *
     * @param OrganizationImage $image
     */
    public function store(OrganizationImage $image)
    {
        $resource = $image->getResource();
        $path = $this->getImagePath($image);
        $this->createDirectoryRecursively(dirname($path));
        file_put_contents($path, $resource);
    }
    
    /**
     * Delete an image file from file system
     *
     * @param OrganizationImage $image
     */
    public function delete(OrganizationImage $image)
    {
        @unlink($this->getImagePath($image));
    }

    /**
     * Match the passed $uri and return an image ID on success
     *
     * @param string $uri
     * @return null|string Image ID
     */
    public function matchUri($uri)
    {
        $pattern = '#^' . preg_quote($this->options->getUriPath(), '#') . '/[0-9a-z]/[0-9a-z]/([0-9a-z]+)\.[a-zA-Z]{3,4}$#';
        $matches = [];
        preg_match($pattern, $uri, $matches);
        
        return isset($matches[1]) ? $matches[1] : null;
    }
    
    /**
     * @param OrganizationImage $image
     * @return string
     */
    protected function getImagePath(OrganizationImage $image)
    {
        return sprintf('%s/%s', $this->options->getFilePath(), $this->getImageSubPath($image));
    }
    
    /**
     * @param OrganizationImage $image
     * @return string
     */
    protected function getImageSubPath(OrganizationImage $image)
    {
        $id = $image->getId();
        
        if (!$id) {
            throw new InvalidArgumentException('image must have ID');
        }
        
        $extension = null;
        $filename = $image->getName();
        
        if ($filename) {
            // get an extension from the filename
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
        } else {
            // try get an extension from MIME if any
            $extension = str_replace('image/', '', $image->getType());
        }
        
        if (!$extension) {
            throw new InvalidArgumentException('unable to get an image file extension');
        }
        
        $firstLevel = substr($id, -1) ?: '0';
        $secondLevel = substr($id, -2, 1) ?: '0';
        
        return sprintf('%s/%s/%s.%s', $firstLevel, $secondLevel, $id, $extension);
    }

    /**
     * @param string $dir
     */
    protected function createDirectoryRecursively($dir)
    {
        $dir = rtrim($dir, '/\\');
        
        if (! is_dir($dir)) {
            $this->createDirectoryRecursively(dirname($dir));
            
            $oldUmask = umask(0);
            
            ErrorHandler::start();
            $created = mkdir($dir, 0777);
            $error = ErrorHandler::stop();
            
            if (!$created) {
                throw new RuntimeException(sprintf('unable to create directory "%s"', $dir), 0, $error);
            }
            
            umask($oldUmask);
        }
    }
}
