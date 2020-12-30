<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Farm;
use App\Form\FarmType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class UpdateFarmHandler
 * @package App\Handler
 */
class UpdateFarmHandler extends AbstractHandler
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
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;
    /**
     * @var ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        ContainerInterface $container,
        HttpClientInterface $httpClient,
        ParameterBagInterface $parameterBag
    ) {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->setFormFactory($container->get('form.factory'));
        $this->httpClient = $httpClient;
        $this->container = $container;
        $this->parameterBag = $parameterBag;
    }

    protected function process($data, array $options): void
    {
        /** @var Farm $data */
        $siretData = $this->httpClient->request(
            Request::METHOD_GET,
            "https://entreprise.data.gouv.fr/api/sirene/v3/etablissements/{$data->getSiret()}"
        )->toArray()['etablissement'];

        $data->setName($siretData['enseigne_1'] ?? $siretData['unite_legale']['denomination']);

        $this->entityManager->flush();
        $this->flashBag->add('success', 'Les informations de votre exploitation ont étés modifiées avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => FarmType::class,
                'form_options' => [
                    'validation_groups' => [
                        'Default',
                        'edit'
                    ]
                ]
            ]
        );
    }
}
