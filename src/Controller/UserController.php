<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handler\UserInfoHandler;
use App\Handler\UserPasswordHandler;
use App\HandlerFactory\HandlerFactoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/account', name: 'account_')]
#[IsGranted('ROLE_USER')]
class UserController extends AbstractController
{
    #[Route('/edit-password', name: 'edit_password')]
    public function editPassword(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(UserPasswordHandler::class);

        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute('account_edit_password');
        }

        return $this->render(
            'ui/account/edit_password.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }

    #[Route('/edit-informations', name: 'edit_informations')]
    public function editInfo(Request $request, HandlerFactoryInterface $handlerFactory): Response
    {
        $handler = $handlerFactory->createHandler(UserInfoHandler::class);

        if ($handler->handle($request, $this->getUser())) {
            return $this->redirectToRoute('account_edit_informations');
        }

        return $this->render(
            'ui/account/edit_informations.html.twig',
            [
                'form' => $handler->createView()
            ]
        );
    }
}
