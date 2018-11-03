<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** Core entity hydrator strategies */
namespace Core\Entity\Hydrator\Strategy;

use Zend\Hydrator\Strategy\StrategyInterface;
use Core\Entity\FileInterface;

/**
 * This strategy copies file entities from on mongo collection to another.
 *
 * This copy process must be done in the same request (means extracting the old
 * entity and hydrating the new entity), because the temporarily file will be
 * removed on script shutdown.
 *
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class FileCopyStrategy implements StrategyInterface
{
    /**
     * Target entity.
     *
     * @var FileInterface
     */
    protected $targetEntity;
    
    /**
     * Creates a new FileCopyStrategy.
     *
     * @param FileInterface $targetEntity
     */
    public function __construct(FileInterface $targetEntity)
    {
        $this->targetEntity = $targetEntity;
    }
    
    /**
     * Returns a clone of the target entity.
     *
     * @return FileInterface
     */
    public function getTargetEntity()
    {
        return clone $this->targetEntity;
    }
    
    /**
     * Extracts the source file entity.
     *
     * Stores the binary content in a temporarily file.
     * Returns the meta data along the name of the temporarily file as an array.
     *
     * If <b>$value</b> is not of the type FileInterface, nothing will be done,
     * and <i>NULL</i> is returned.
     *
     * @param FileInterface|null $value
     * @return array|null
     * @see \Zend\Hydrator\Strategy\StrategyInterface::extract()
     */
    public function extract($value)
    {
        if (!$value instanceof FileInterface) {
            return null;
        }
        
        // Store binary data in temporary file.
        $tmp = tempnam(sys_get_temp_dir(), 'yk-copy.');
        $out = fopen($tmp, 'w');
        $in  = $value->getResource();
        
        // ensures garbage removal
        register_shutdown_function(
            function ($filename) {
                @unlink($filename);
            },
            $tmp
        );
        
        while (!feof($in)) {
            fputs($out, fgets($in, 1024));
        }
        fclose($in);
        fclose($out);

        $return = ["file" => $tmp];

        foreach (['user', 'name', 'type'] as $key) {
            $v = $value->{"get$key"}();
            if ($v) {
                $return[$key]=$v;
            }
        }

        return $return;
    }
    
    /**
     * Hydrates and returns a clone of the target entity.
     *
     * <b>$value</b> must be either null or the array returned from
     * {@link extract()}.
     *
     * if <b>$value</b> is null, nothing is done and <i>NULL</i> is returned.
     *
     * @param array|null $value
     * @return FileInterface
     * @see \Zend\Hydrator\Strategy\StrategyInterface::hydrate()
     */
    public function hydrate($value)
    {
        if (!is_array($value)) {
            return null;
        }

        $entity = $this->getTargetEntity();

        foreach ($value as $key=>$v) {
            $entity->{"set$key"}($v);
        }
        return $entity;
    }
}
