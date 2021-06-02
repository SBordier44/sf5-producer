<?php

namespace App\Controller;

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

        if ($this->getUser() && $this->getUser()::ROLE === Producer::ROLE) {
            $params = [
                'orders' => $orderRepository->getOrdersWaitValidationForProducer(
                    $this->getUser()
                )
            ];
            dump($params['orders']);
        }

        return $this->render(
            'ui/index.html.twig',
            $params
        );
    }
}
