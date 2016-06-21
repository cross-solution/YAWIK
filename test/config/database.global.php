<?php
/**
 * YAWIK
 *
 * @filesource
 * @author    Carsten Bleek <bleek@cross-solution.de>
 * @copyright 2013-2016 Cross Solution (http://cross-solution.de)
 * @version   GIT: $Id$
 * @license   https://yawik.org/LICENSE.txt MIT
 */
/**
 * defines connection settings to a test database
 */

return ['doctrine' => array(
    'connection' => [
        'odm_default' => [
            'connectionString' => 'mongodb://localhost:27017/YAWIK_TEST',
        ],
    ],
    'configuration' => array(
        'odm_default' => array(
            'default_db' => 'YAWIK_TEST',
        ),
    ),
)];