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

namespace Sylius\BootstrapAdminUi\Symfony\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Resource\Metadata\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

final class QuickEditFormController
{
    public function __construct(
        private GridProviderInterface $gridProvider,
        private RegistryInterface $resourceMetadataRegistry,
        private ManagerRegistry $managerRegistry,
        private FormFactoryInterface $formFactory,
        private Environment $twig,
    ) {
    }

    public function __invoke(Request $request, string $gridCode, mixed $id): Response
    {
        $grid = $this->gridProvider->get($gridCode);

        if (!$grid->hasQuickEditableFields()) {
            throw new NotFoundHttpException(sprintf('Grid "%s" has no quick-editable fields.', $gridCode));
        }

        $resourceClass = $grid->getDriverConfiguration()['class'] ?? null;

        if (null === $resourceClass) {
            throw new \LogicException(sprintf('Grid "%s" has no resolvable resource class for quick actions.', $gridCode));
        }

        $metadata = $this->resourceMetadataRegistry->getByClass($resourceClass);
        $resource = $this->managerRegistry->getRepository($resourceClass)->find($id);

        if (null === $resource) {
            throw new NotFoundHttpException(sprintf('Resource "%s" with id "%s" does not exist.', $resourceClass, $id));
        }

        $form = $this->formFactory->create($metadata->getClass('form'), $resource);
        $form->handleRequest($request);

        return new Response($this->twig->render('@SyliusBootstrapAdminUi/shared/grid/quick_edit/fragment.html.twig', [
            'form' => $form->createView(),
            'fieldNames' => array_keys($grid->getQuickEditableFields()),
        ]));
    }
}
