<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Producer;
use App\Entity\User;
use App\Form\RegistrationType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class RegistrationHandler
 * @package App\Handler
 */
class RegistrationHandler extends AbstractHandler
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;
    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        UserPasswordEncoderInterface $passwordEncoder,
        ContainerInterface $container,
        HttpClientInterface $httpClient
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->passwordEncoder = $passwordEncoder;
        $this->setFormFactory($container->get('form.factory'));
        $this->httpClient = $httpClient;
    }

    protected function process($data, array $options): void
    {
        /** @var User $data */

        if ($data instanceof Producer) {
            $siretData = $this->httpClient->request(
                Request::METHOD_GET,
                "https://entreprise.data.gouv.fr/api/sirene/v3/etablissements/{$data->getFarm()->getSiret()}"
            )->toArray()['etablissement'];

            $data->getFarm()->setName($siretData['enseigne_1'] ?? $siretData['unite_legale']['denomination']);
        }
        $data->setPassword($this->passwordEncoder->encodePassword($data, $data->getPlainPassword()));
        $this->entityManager->persist($data);
        $this->entityManager->flush();
        $this->flashBag->add('success', 'Votre inscription a été effectuée avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => RegistrationType::class,
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
