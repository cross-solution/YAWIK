<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** SearchableEntityinterface.php */
namespace Core\Entity;

interface SearchableEntityInterface
{
    
    /**
     * @return array searchable properties names.
     */
    public function getSearchableProperties();
    public function setKeywords(array $keywords);
    public function clearKeywords();
    public function getKeywords();
}
