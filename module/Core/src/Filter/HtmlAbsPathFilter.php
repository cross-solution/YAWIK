<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright (c) 2013 - 2016 Cross Solution (http://cross-solution.de)
 * @license   MIT
 * @author    weitz@cross-solution.de
 */

namespace Core\Filter;

use Zend\Filter\FilterInterface;
use Zend\Filter\Exception;
use Zend\Dom\Query;

use Zend\Uri\Http;

class HtmlAbsPathFilter implements FilterInterface
{
    protected $uri;

    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function filter($html)
    {
        $baseUri = new Http($this->uri);

        $dom = new Query($html);
        $results = $dom->execute('*[@href],*[@src]');
        foreach ($results as $result) {
            $attributeMap = $result->attributes;
            foreach ($attributeMap as $attribute) {
                $name = $attribute->name;
                if ($name == 'href' || $name == 'src') {
                    $value = $result->getAttribute($name);
                    $uri = new Http($value);
                    $h = $uri->resolve($baseUri);
                    $result->setAttribute($name, $h);
                }
            }
        }
        $document = $results->getDocument();
        $documentHTML = $document->saveHTML();
        return $documentHTML;
    }
}
