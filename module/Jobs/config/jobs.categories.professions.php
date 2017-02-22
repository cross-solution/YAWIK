<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

/*
 * Format:
 * [
 *      'name' => <name>, [required]
 *      'value' => <value>, [optional]
 *      'children' => [ // optional
 *          <name> // strings will be treated as ['name' => <name>]
 *          [
 *              'name' => <name>, [required]
 *              'value' => <value>, [optional]
 *              'children' => [ ... ]
 *       ]
 * ]
 */

return [
    'name' => 'Professions',
    'children' => [
        'Sales',
        'Law',
        'HR',
        'IT',
        'Banking, Insurances',
        'Engineering',
        'Design',
        'Marketing and Communication',
        'Senior Management',
        'Education and Social Policy',
        'Public Sector',
        'Medizin',
        'Manufacturing',
        'Sciences and Research',
        'Administration and Secretariat',
        'Finance',
        'Physicians',
    ]
];

