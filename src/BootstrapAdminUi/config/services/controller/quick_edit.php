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

use Sylius\BootstrapAdminUi\Symfony\Controller\QuickEditFormController;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('sylius_bootstrap_admin_ui.controller.quick_edit', QuickEditFormController::class)
        ->public()
        ->args([
            service('sylius.grid.provider'),
            service('sylius.resource_registry'),
            service('doctrine'),
            service('form.factory'),
            service('twig'),
        ])
    ;
    $services->alias(QuickEditFormController::class, 'sylius_bootstrap_admin_ui.controller.quick_edit');
};
