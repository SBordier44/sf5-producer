<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\FacilitySiretDto;
use App\Entity\Producer;
use App\Form\SiretType;
use App\Handler\RegistrationHandler;
use App\HandlerFactory\HandlerFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProducerRegistrationController extends AbstractController
{
    #[Route('/registration/producer/steps/one', name: 'security_registration_producer_step_one')]
    public function stepOne(
        Request $request,
        SessionInterface $session
    ): Response {
        $enterprise = new FacilitySiretDto();

        $form = $this->createForm(SiretType::class, $enterprise);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('stepOne', $enterprise);

            return $this->redirectToRoute('security_registration_producer_step_two');
        }

        return $this->render('ui/registration/producer/step_one.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/registration/producer/steps/two', name: 'security_registration_producer_step_two')]
    public function stepTwo(Request $request, SessionInterface $session, HandlerFactory $handlerFactory): Response
    {
        if (!$session->has('stepOne')) {
            return $this->redirectToRoute('security_registration_producer_step_one');
        }

        $producer = new Producer();
        $producer->getFarm()->setSiret($session->get('stepOne')->getSiret());

        $handler = $handlerFactory->createHandler(RegistrationHandler::class);

        if ($handler->handle($request, $producer)) {
            return $this->redirectToRoute('security_login');
        }

        return $this->render('ui/registration/register.html.twig', [
            'registrationForm' => $handler->createView(),
            'producer' => $producer,
            'role' => 'producer'
        ]);
    }
}
