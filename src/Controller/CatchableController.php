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
use Forci\Bundle\Catchable\Repository\CatchableRepository;
use Forci\Bundle\Catchable\Serializer\CatchableSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CatchableController extends AbstractController {

    /** @var CatchableRepository */
    private $repository;

    /** @var CatchableSerializer */
    private $serializer;

    public function __construct(
        CatchableRepository $repository, CatchableSerializer $serializer
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    public function listAction(Request $request) {
        try {
            $filter = new CatchableFilter();
            $filterForm = $this->createForm(CatchableFilterType::class, $filter);
            $filter->loadFromRequest($request, $filterForm);

            $formatted = [];
            /** @var Catchable $entity */
            foreach ($this->repository->filter($filter) as $entity) {
                $formatted[] = $this->serializer->serialize($entity);
            }

            $data = [
                'success' => true,
                'entities' => $formatted,
                'total' => $this->repository->countForFilter($filter),
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
        try {
            /** @var Catchable $entity */
            $entity = $this->repository->findOneById($id);

            $data = [
                'success' => true,
                'entity' => $entity ? $this->serializer->serialize($entity) : null
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
            if ($id = $request->query->get('id')) {
                $this->repository->removeById($id);
            }

            if ($class = $request->query->get('class')) {
                $this->repository->removeByClass($class);
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
        $data = [
            'success' => true
        ];

        try {
            $this->repository->removeAll();
        } catch (\Throwable $e) {
            $data = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return $this->json($data);
    }
}
