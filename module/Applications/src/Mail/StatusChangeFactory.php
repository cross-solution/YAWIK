<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Applications\Mail;

use Psr\Container\ContainerInterface;

/**
 * Factory for \Applications\Mail\StatusChange
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class StatusChangeFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): StatusChange {
        return new StatusChange(
            $container->get('Router'),
            $options
        );
    }
}
