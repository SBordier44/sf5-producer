<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Farm;
use App\Entity\Producer;
use App\Handler\UpdateFarmHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use App\Repository\FarmRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/farm', name: 'farm_')]
class FarmController extends AbstractController
{
    #[Route('/all', name: 'all')]
    public function all(FarmRepository $farmRepository, SerializerInterface $serializer): JsonResponse
    {
        $farms = $serializer->serialize($farmRepository->findAll(), 'json', ['groups' => 'json_read']);

        return new JsonResponse($farms, Response::HTTP_OK, [], true);
    }

    #[Route('/{slug}/show', name: 'show')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(Farm $farm, ProductRepository $productRepository): Response
    {
        return $this->render(
            'ui/farm/show.html.twig',
            [
                'farm' => $farm,
                'products' => $productRepository->findBy(['farm' => $farm])
            ]
        );
    }

    #[Route('/update', name: 'update')]
    #[IsGranted('ROLE_PRODUCER')]
    public function update(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        /** @var Producer $producer */
        $producer = $this->getUser();

        $handler = $handlerFactory->createHandler(UpdateFarmHandler::class);

        if ($handler->handle($request, $producer->getFarm())) {
            return $this->redirectToRoute('farm_update');
        }

        return $this->render(
            'ui/farm/update.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }
}
