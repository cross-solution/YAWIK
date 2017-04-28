<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright  2013 - 2017 Cross Solution <http://cross-solution.de>
 */

/*
 * This file is used to create the industries initially. If you want to modify industries copy this file into
 * the config/autoload directory and adjust the categories.
 *
 * The categories are imported, if there is no "jobs.categories" collection. So after you've modifies the categories
 * drop your "jobs.categories" and reload a YAWIK page, which accesses categories.
 *
 * This file ist only used, if there ist no "config/autoload/jobs.categories.industries.php" available.
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
    'name' => 'Industries',
    'children' => [
        'Advertising, Communication & PR',
        'Banking',
        'Building & Construction',
        'Mining & Quarrying',
        'Education & Training',
        'Chemical & Petrochemical Industry',
        'Manufacture of pulp, paper and paper products',
        'Manufacture of electrical and optical equipment',
        'Electricity, Gas & Water Supply',
        'Manufacture of Transport Equipment',
        'Financial Services',
        'Recreational, Cultural & Sporting Activities',
        'Medical, Health & Social Care',
        'Manufacture of other non-metallic mineral products',
        'Wholesale, Retail Trade',
        'Handcraft',
        'Manufacture of wood and wood products',
        'Hotels, Restaurants & Catering',
        'Real Estate',
        'IT & Internet',
        'Fast Moving Consumer Goods/ Durables',
        'Agriculture, Fishing & Forestry',
        'Space and Aerospace',
        'Manufacture of machinery and equipment',
        'Publishing, Printing & Reproduction',
        'Medical Technology',
        'Steel Industry',
        'Food & Beverages',
        'Public Administration & Defence',
        'HR Services, Recruitment & Selection',
        'Pharmaceutical Sector',
        'Other Sectors and Industries',
        'Other Business Activities & Services',
        'Other Manufacturing',
        'Telecommunication Services',
        'Textiles, Clothing & Leather, Fashion',
        'Distribution, Transport & Logistics',
        'Legal, Consultancy & Auditing',
        'Insurances',
        'Research & Development'
    ]
];

