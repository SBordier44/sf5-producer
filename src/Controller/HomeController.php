<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(
        OrderRepository $orderRepository
    ): Response {
        $params = [];

        /** @var null|Customer|Producer $user */
        $user = $this->getUser();

        if ($user && $user::ROLE === Producer::ROLE) {
            $params = [
                'orders' => $orderRepository->getOrdersWaitValidationForProducer(
                    $user
                )
            ];
        }

        return $this->render(
            'ui/index.html.twig',
            $params
        );
    }
}
