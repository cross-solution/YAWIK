<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Applications\Factory\Mail;

use Applications\Entity\Attachment;
use Applications\Mail\ApplicationCarbonCopy;
use Applications\Mail\Forward;
use Psr\Container\ContainerInterface;

/**
 * Factory for \Applications\Mail\Forward
 *
 * @author Mathias Gelhausen
 */
class ForwardFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): Forward {
        $class = strpos($requestedName, 'Copy') === false ? Forward::class : ApplicationCarbonCopy::class;

        return new $class(
            $container->get('ViewHelperManager'),
            $container->get('repositories')->get(Attachment::class),
            $options
        );
    }
}
