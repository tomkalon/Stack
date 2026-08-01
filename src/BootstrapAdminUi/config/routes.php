<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('sylius_bootstrap_admin_ui_quick_edit', '/_quick-edit/{gridCode}/{id}')
        ->controller('sylius_bootstrap_admin_ui.controller.quick_edit')
        ->methods(['GET', 'PATCH'])
    ;
};
