<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Organizations\Form\Element;

use Core\Form\ViewPartialProviderInterface;
use Core\Form\ViewPartialProviderTrait;
use Laminas\Form\Element\Radio;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class FeaturedCompaniesFilterRadio extends Radio implements ViewPartialProviderInterface
{
    use ViewPartialProviderTrait;

    private $defaultPartial = 'organizations/form/featured-companies-filter-radio';

    public function init()
    {
        $this->setName('featured');
        $this->setValueOptions([
            'none' => /*@translate*/ 'All',
            'on' => /*@translate*/ 'Featured',
            'off' => /*@translate*/ 'Not Featured',
        ]);
        $this->setAttributes([
            'value' => 'none',
            'data-submit-on-change' => 'true'
        ]);
    }
}
