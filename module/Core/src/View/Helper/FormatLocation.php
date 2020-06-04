<?php

/**
 * Aviation
 *
 */

declare(strict_types=1);

namespace Core\View\Helper;

use Core\Entity\LocationInterface;
use Doctrine\Common\Collections\Collection;
use Laminas\View\Helper\AbstractHelper;

/**
 * TODO: description
 *
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * TODO: write tests
 */
class FormatLocation extends AbstractHelper
{
    protected $defaultFormat = '%S%Z%r%C';

    public function __invoke($location = null, ?string $format = null, $separator = '<br>')
    {
        if ($location === null) {
            return $this;
        }

        if ($location instanceof Collection) {
            return $this->formatCollection($location, $format, $separator);
        }

        return $this->format($location, $format);
    }

    public function format(LocationInterface $location, ?string $format = null)
    {
        if ($format == null) {
            $format = $this->defaultFormat;
        }

        $placeholders = [];
        preg_match_all('~%[cCzZrsSn](?::[^\s%]*)?~s', $format, $matches);

        $variables = [
            '%c' => "getCity",
            '%C' => "getCountry",
            '%z' => "getPostalCode",
            '%Z' => ["getPostalCode", "getCity"],
            '%r' => "getRegion",
            '%s' => "getStreetname",
            '%S' => ["getStreetname", "getStreetnumber"],
            '%n' => "getStreetnumber",
        ];

        $str = str_replace('%%', ':percent:', $format);

        foreach ($matches[0] as $var) {
            if (strpos($var, ':') !== false) {
                [$ph, $sep] = explode(':', $var, 2);
            } else {
                $ph = $var;
                $sep = null;
            }

            $method = $variables[$ph];
            if (is_array($method)) {
                $val = [];
                foreach ($method as $m) {
                    $val[] = $location->$m();
                }
                $val = join(' ', $val);
            } else {
                $val = $location->$method();
            }

            $val = trim((string) $val);

            if (!$val) {
                $str = str_replace($var, '', $str);
                continue;
            }

            $placeholders[] = [$var, $val, $sep];
        }

        while ($placeholder = array_shift($placeholders)) {
            [$var, $val, $sep] = $placeholder;

            if (count($placeholders)) {
                $val .= $sep === null ? ', ' : $sep;
            }

            $str = str_replace($var, $val, $str);
        }

        if (strpos($format, '%lon') !== false || strpos($format, '%lat' !== false)) {
            $coords = $location->getCoordinates()->getCoordinates();
            $str = str_replace(['%lon', '%lat'], [$coords[0], $coords[1]], $str);
        }

        $str = str_replace([':percent:', ':s:'], ['%', ' '], $str);
        return $str;
    }

    public function formatCollection($locations, $format, $separator = '<br>')
    {
        $loc = [];

        foreach ($locations as $l) {
            $loc[] = $this->format($l, $format);
        }

        return join($separator, $loc);
    }
}
