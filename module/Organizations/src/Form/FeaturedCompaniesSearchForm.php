<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Organizations\Form;

use Core\Form\SearchForm;
use Organizations\Form\Element\FeaturedCompaniesFilterRadio;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class FeaturedCompaniesSearchForm extends SearchForm
{

    public function init()
    {
        $this->setName($this->getOption('name') ?: 'searchform');

        $this->addTextElement(
            $this->getOption('text_name') ?: 'q',
            $this->getOption('text_label') ?: /*@translate*/ 'Search',
            $this->getOption('text_placeholder') ?: /*@translate*/ 'Search query',
            $this->getOption('text_span') ?: 12,
            50,
            true
        );

        $this->add([
            'type' => FeaturedCompaniesFilterRadio::class
        ]);

        $this->addButton(/*@translate*/ 'Search', -1000, 'submit');
        $this->addButton(/*@translate*/ 'Clear', -1001, 'reset');

        $this->addElements();
    }
}
