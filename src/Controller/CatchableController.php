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
use Forci\Bundle\Catchable\Serializer\CatchableSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CatchableController extends Controller {

    public function listAction(Request $request) {
        $repo = $this->get('forci.catchable.repo.catchable');
        $serializer = $this->get(CatchableSerializer::class);

        try {
            $filter = new CatchableFilter();
            $filterForm = $this->createForm(CatchableFilterType::class, $filter);
            $filter->loadFromRequest($request, $filterForm);

            $formatted = [];
            /** @var Catchable $entity */
            foreach ($repo->filter($filter) as $entity) {
                $formatted[] = $serializer->serialize($entity);
            }

            $data = [
                'success' => true,
                'entities' => $formatted,
                'total' => $repo->countForFilter($filter),
                'page' => $filter->getPage(),
                'limit' => $filter->getLimit()
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
        $serializer = $this->get(CatchableSerializer::class);

        try {
            /** @var Catchable $entity */
            $entity = $repo->findOneById($id);

            $data = [
                'success' => true,
                'entity' => $entity ? $serializer->serialize($entity) : null
            ];
        } catch (\Throwable $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->json($data);
    }

    public function deleteAction(Request $request) {
        try {
            $repo = $this->get('forci.catchable.repo.catchable');

            if ($id = $request->query->get('id')) {
                $repo->removeById($id);
            }

            if ($class = $request->query->get('class')) {
                $repo->removeByClass($class);
            }

            return $this->json([
                'success' => true
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
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
