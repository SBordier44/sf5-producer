<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Address as AddressEntity;
use App\Entity\Position;
use App\Entity\Producer;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\HandlerFactory\AbstractHandler;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegistrationHandler extends AbstractHandler
{
    #[Pure]
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FlashBagInterface $flashBag,
        private UserPasswordHasherInterface $passwordEncoder,
        FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
        private EmailVerifier $emailVerifier
    ) {
        parent::__construct($formFactory);
    }

    protected function process($data, array $options): void
    {
        /** @var User $data */
        if ($data instanceof Producer) {
            $siretData = $this->httpClient->request(
                Request::METHOD_GET,
                "https://entreprise.data.gouv.fr/api/sirene/v3/etablissements/{$data->getFarm()->getSiret()}"
            )->toArray()['etablissement'];

            $address = $data->getFarm()->getAddress()
                ->setAddress(
                    AddressEntity::buildReadableSireneAddressStreet([
                        $siretData['numero_voie'],
                        $siretData['indice_repetition'],
                        $siretData['type_voie'],
                        $siretData['libelle_voie']
                    ])
                )
                ->setAddressExtra($siretData['complement_adresse'])
                ->setCity($siretData['libelle_commune'])
                ->setCountry('France')
                ->setPosition(
                    (new Position())
                        ->setLatitude((float)$siretData['latitude'])
                        ->setLongitude((float)$siretData['longitude'])
                )
                ->setZipCode($siretData['code_postal']);


            $data->getFarm()
                ->setName(
                    $siretData['enseigne_1']
                    ?? $siretData['denomination_usuelle']
                    ?? $siretData['unite_legale']['denomination']
                )
                ->setAddress($address);
        }

        $data->setPassword($this->passwordEncoder->hashPassword($data, $data->getPlainPassword()));

        $this->entityManager->persist($data);

        $this->entityManager->flush();

        $this->emailVerifier->sendEmailConfirmation(
            'security_verify_email',
            $data,
            (new TemplatedEmail())
                ->from(new Address('hello@mon-petit-producteur.fr', 'Mon Petit Producteur'))
                ->to(new Address($data->getEmail(), $data->getFullName()))
                ->subject('Veuillez confirmer votre adresse Email')
                ->htmlTemplate('ui/registration/confirmation_email.html.twig')
        );

        $this->flashBag->add('success', 'Votre inscription a été effectuée avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => RegistrationFormType::class,
                'form_options' => [
                    'validation_groups' => [
                        'Default',
                        'password'
                    ]
                ]
            ]
        );
    }
}
