<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Producer;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Uid\Uuid;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration/{role}", name="security_registration")
     * @param string $role
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function registration(
        string $role,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $user = Producer::ROLE === $role ? new Producer() : new Customer();
        $form = $this->createForm(RegistrationType::class, $user)->handleRequest($request);
        $user->setId(Uuid::v4());

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Votre inscription à été effectuée avec succès');
            return $this->redirectToRoute('index');
        }

        return $this->render(
            'ui/security/registration.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @Route("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render(
            'ui/security/login.html.twig',
            [
                'last_username' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError()
            ]
        );
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
    }
}
