<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** PaginationList.php */
namespace Applications\Repository;

/**
 * class for accessing a pagination list.
 */
class PaginationList
{
    
    /**
     * List of ids.
     * @var array
     */
    protected $ids = array();
    
    /**
     * Pointer
     * @var int|bool
     */
    protected $pointer = false;
    
    /**
     * Count of list items
     * @var int
     */
    protected $count = 0;
    
    /**
     * Creates a new PaginationList
     * @param array $ids
     */
    public function __construct(array $ids = array())
    {
        $this->setList($ids);
    }
    
    /**
     * Sets the list entries
     *
     * @param array $ids
     * @return \Applications\Repository\PaginationList
     */
    public function setList(array $ids)
    {
        $this->ids = $ids;
        $this->count = count($ids);
        return $this;
    }
    
    /**
     * Set current list entry (move pointer).
     *
     * @param string $id
     * @return int|bool
     */
    public function setCurrent($id)
    {
        $this->pointer = array_search($id, $this->ids);
        return $this->pointer;
    }
    
    /**
     * Gets current list entry.
     *
     * @return NULL|array
     */
    public function getCurrent()
    {
        if (false === $this->pointer || empty($this->ids)) {
            return null;
        }
        return $this->ids[$this->pointer];
    }
    
    /**
     * Gets the current position.
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->pointer + 1;
    }
    
    /**
     * Gets the total count.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
    
    /**
     * gets the id BEFORE the current entry.
     *
     * @return null|string
     */
    public function getPrevious()
    {
        if (false === $this->pointer || !$this->count || 0 == $this->pointer) {
            return null;
        }
        return $this->ids[$this->pointer - 1];
    }
    
    /**
     * Gets the id BEHIND the current entry.
     *
     * @return string
     * @return NULL|multitype:
     */
    public function getNext()
    {
        if (false === $this->pointer || !$this->count || $this->count == $this->pointer) {
            return null;
        }
        
        $pointer = $this->pointer + 1;
        
        return isset($this->ids[$pointer]) ? $this->ids[$pointer] : null;
    }
}
