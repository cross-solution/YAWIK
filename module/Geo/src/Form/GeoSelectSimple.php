<?php
/**
 * YAWIK
 *
 * @filesource
 * @author    Carsten Bleek <bleek@cross-solution.de>
 * @copyright 2013-2017 Cross Solution (http://cross-solution.de)
 * @version   GIT: $Id$
 * @license   https://yawik.org/LICENSE.txt MIT
 */

namespace Geo\Form;

use Core\Form\Hydrator\HydratorStrategyProviderInterface;
use Core\Form\Hydrator\HydratorStrategyProviderTrait;
use Jobs\Entity\Location;
use Core\Form\Element\Select;
use Zend\Hydrator\Strategy\ClosureStrategy;

/**
 * Class GeoSelectSimple
 *
 * This fieldset can be used, if you want to use a select field with a certain amount of locations.
 *
 * Example: You can configure the location field to hold the 3 locations "Stuttgart", "München" and "Frankfurt" by
 * configuring the location field the following way:
 *
 *   'l' => [
 *       'options' => [
 *           'value_options' => [
 *               '{"city":"Stuttgart","region":"Baden-Württemberg","coordinates":{"type":"Point","coordinates":[9.17702,48.78232]}}' => 'Stuttgart',
 *               '{"city":"München","region":"Bayern","coordinates":{"type":"Point","coordinates":[11.57549,48.13743]}}' => 'München',
 *               '{"city":"Frankfurt","region":"Hessen","coordinates":{"type":"Point","coordinates":[8.68212,50.11092]}}' => 'Frankfurt',
 *           ]
 *       ],
 *       'attributes' => [
 *            'class' => '',
 *            'data-searchbox' => '-1',
 *            'data-placeholder' => 'please select',
 *            'data-allowclear' => 'true',
 *        ],
 *       'type' => 'SimpleLocationSelect',
 *       'enabled' => true
 *   ],
 *
 * @package Geo\Form
 */
class GeoSelectSimple extends Select implements HydratorStrategyProviderInterface
{
    use HydratorStrategyProviderTrait;

    private function getDefaultHydratorStrategy()
    {
        return new ClosureStrategy(
            /* extract */
            function ($value) {
                if ($value instanceof Location) {
                    return $value->toString();
                }

                if (0 === strpos($value, '{')) {
                    return $value;
                }
                if ($value) {
                    foreach ($this->getValueOptions() as $optValue => $opt) {
                        if (false !== strpos($value, $opt)) {
                            return $optValue;
                        }
                    }
                }

                return null;
            },

            /* hydrate */
            function ($value) {
                if (empty($value) || 0 !== strpos($value, '{')) {
                    return null;
                }

                $location = new Location();
                return $location->fromString($value);
            }
        );
    }
}
