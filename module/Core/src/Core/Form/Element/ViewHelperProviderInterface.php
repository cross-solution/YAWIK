<?php

namespace Core\Form\Element;

interface ViewHelperProviderInterface
{
    /**
     * Gets the view helper instance or service name.
     *
     * @return \Zend\View\Helper\HelperInterface|string
     */
    public function getViewHelper();

}
