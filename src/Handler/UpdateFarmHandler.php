<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\Farm;
use App\Form\FarmType;
use App\HandlerFactory\AbstractHandler;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class UpdateFarmHandler extends AbstractHandler
{
    #[Pure]
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        private HttpClientInterface $httpClient,
    ) {
        parent::__construct($formFactory);
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

            ]
        );
    }
}
