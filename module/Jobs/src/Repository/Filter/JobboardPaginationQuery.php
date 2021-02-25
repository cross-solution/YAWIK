<?php

declare(strict_types=1);

/**
 * YAWIK
 *
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license   MIT
 */

namespace Jobs\Repository\Filter;

use Core\Repository\Filter\AbstractPaginationQuery;
use DateTime;
use Jobs\Entity\StatusInterface;
use MongoDB\BSON\ObjectId;

/**
 * maps query parameters to entity attributes
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 */
class JobboardPaginationQuery extends AbstractPaginationQuery
{

    public function createQuery($params, $queryBuilder)
    {
        if (isset($params['q'])) {
            $params['search'] = $params['q'];
        }

        $queryBuilder->field('status.name')->equals(StatusInterface::ACTIVE);

        /*
         * search jobs by keywords
         */
        if (isset($params['search']) && !empty($params['search'])) {
            $search = strtolower($params['search']);
            $queryBuilder->text($search);
        }
        if (isset($params['o']) && !empty($params['o'])) {
            $queryBuilder->field('organization')->equals(new ObjectId($params['o']));
//            $queryBuilder->field('metaData.companyName')->equals(new \MongoRegex('/' . $params['o'] . '/i'));
        }

        if (isset($params['l'])) {
            $coords = $params['l']->getCoordinates()->getCoordinates();
            $queryBuilder->field('locations.coordinates')->geoWithinCenter((float) $coords[0], (float) $coords[1], (float) $this->value['d'] / 100);
        }

        if (isset($params['channel']) && !empty($params['channel']) && $params['channel'] != "default") {
            $queryBuilder->field('portals')->equals($params['channel']);
        }

        if (isset($params['publishedSince'])) {
            $publishedSince = $params['publishedSince'];

            if (!$publishedSince instanceof DateTime) {
                $publishedSince = new DateTime($publishedSince);
            }

            $queryBuilder->field('datePublishStart.date')->gte($publishedSince);
        }

        if (isset($this->value['sort'])) {
            foreach (explode(",", $this->value['sort']) as $sort) {
                $queryBuilder->sort($this->filterSort($sort));
            }
        }

        return $queryBuilder;
    }

    protected function filterSort($sort)
    {
        if ($sort[0] == '-') {
            $sortProp = substr($sort, 1);
            $sortDir  = -1;
        } else {
            $sortProp = $sort;
            $sortDir = 1;
        }
        switch ($sortProp) {
            case "date":
                $sortProp = "datePublishStart.date";
                break;
            case "title":
                $sortProp = "title";
                break;
            case "cam":
                $sortProp = "atsEnabled";
                break;

            default:
                break;
        }
        return array($sortProp => $sortDir);
    }
}
