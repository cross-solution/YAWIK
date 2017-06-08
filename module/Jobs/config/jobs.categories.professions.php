<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

/*
 * This file is used to create the professions initially. If you want to modify professions copy this file into
 * the config/autoload directory and adjust the categories.
 *
 * The categories are imported, if there is no "jobs.categories" collection. So after you've modifies the categories
 * drop your "jobs.categories" and reload a YAWIK page, which accesses categories.
 *
 * This file ist only used, if there ist no "config/autoload/jobs.categories.professions.php" available.
 *
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

