<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 */

/** EagerCursor.php */
namespace Core\Repository\DoctrineMongoODM\PaginatorAdapter;

use Doctrine\ODM\MongoDB\EagerCursor as DoctrineEagerCursor;

class EagerCursor extends \DoctrineMongoODMModule\Paginator\Adapter\DoctrinePaginator
{
    public function __construct(DoctrineEagerCursor $cursor)
    {
        $this->cursor = $cursor;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $cursor = clone $this->cursor;
    
        //$cursor->recreate();
        $cursor->skip($offset);
        $cursor->limit($itemCountPerPage);
    
        return $cursor;
    }
}
