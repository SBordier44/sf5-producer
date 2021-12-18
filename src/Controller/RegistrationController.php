<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Handler\RegistrationHandler;
use App\HandlerFactory\HandlerFactory;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'security_register')]
    public function register(
        Request $request,
        HandlerFactory $handlerFactory
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }

        $user = new Customer();

        $handler = $handlerFactory->createHandler(RegistrationHandler::class);

        if ($handler->handle($request, $user)) {
            return $this->redirectToRoute('security_login');
        }

        return $this->render('ui/registration/register.html.twig', [
            'registrationForm' => $handler->createView(),
            'role' => 'customer'
        ]);
    }

    #[Route('/verify/email', name: 'security_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository,
        EmailVerifier $emailVerifier
    ): Response {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('security_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('security_register');
        }

        try {
            $emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('security_login');
        }

        $this->addFlash('success', 'Votre adresse Email a été confirmée avec succès.');

        return $this->redirectToRoute('security_login');
    }
}
