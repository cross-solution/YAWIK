<?php

/**
 * YAWIK
 *
 * @see       https://github.com/cross-solution/YAWIK for the canonical source repository
 * @copyright https://github.com/cross-solution/YAWIK/blob/master/COPYRIGHT
 * @license   https://github.com/cross-solution/YAWIK/blob/master/LICENSE
 */

declare(strict_types=1);

namespace Core\Queue\Strategy;

use Psr\Container\ContainerInterface;

/**
 * Factory for \Core\Queue\Strategy\SendMailStrategy
 *
 * @author Mathias Gelhausen
 * TODO: write tests
 */
class SendMailStrategyFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $requestedName,
        ?array $options = null
    ): SendMailStrategy {
        return new SendMailStrategy(
            $container->get('Core/MailService')
        );
    }
}
