<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2016 Cross Solution <http://cross-solution.de>
 */
  
/** */
namespace Core\Repository;

/**
 * ${CARET}
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
interface DraftableEntityAwareInterface
{
    /**
     * Find entities in draft mode.
     *
     * Sets the key 'isDraft' with the value true and proxies to parent::findBy()
     *
     * @param array $criteria
     * @param array|null $sort
     * @param null|int  $limit
     * @param null|int  $skip
     *
     * @return \MongoCursor
     */
    public function findDraftsBy(array $criteria, array $sort = null, $limit = null, $skip = null);

    /**
     * Finds one entity in draft mode.
     *
     * Sets the key 'isDraft' to true and proxies to findOneBy()
     *
     * @param array $criteria
     *
     * @return \MongoCursor
     */
    public function findOneDraftBy(array $criteria);
}
