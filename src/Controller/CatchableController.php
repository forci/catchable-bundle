<?php

/*
 * This file is part of the ForciCatchableBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Tatyana Mincheva <tatjana@forci.com>
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\Catchable\Controller;

use Forci\Bundle\Catchable\Entity\Catchable;
use Forci\Bundle\Catchable\Filter\CatchableFilter;
use Forci\Bundle\Catchable\Form\CatchableFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CatchableController extends Controller {

    public function listAction(Request $request) {
        $repo = $this->get('forci.catchable.repo.catchable');

        try {
            $filter = new CatchableFilter();
            $filterForm = $this->createForm(CatchableFilterType::class, $filter);
            $filter->loadFromRequest($request, $filterForm);

            $formatted = [];
            /** @var Catchable $entity */
            foreach ($repo->filter($filter) as $entity) {
                $formatted[] = $entity->toArray();
            }

            $data = [
                'success' => true,
                'entities' => $formatted,
                'total' => $repo->countForFilter($filter)
            ];
        } catch (\Throwable $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->json($data);
    }

    public function getAction(int $id) {
        $repo = $this->get('forci.catchable.repo.catchable');
        try {
            /** @var Catchable $entity */
            $entity = $repo->findOneById($id);

            $data = [
                'success' => true,
                'entity' => $entity ? $entity->toArray() : null
            ];
        } catch (\Throwable $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->json($data);
    }

    public function deleteAction(int $id) {
        $repo = $this->get('forci.catchable.repo.catchable');
        $data = [
            'success' => true
        ];

        try {
            $repo->removeById($id);
        } catch (\Throwable $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->json($data);
    }

    public function deleteAllAction() {
        $repo = $this->get('forci.catchable.repo.catchable');
        $data = [
            'success' => true
        ];

        try {
            $repo->removeAll();
        } catch (\Throwable $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->json($data);
    }
}
