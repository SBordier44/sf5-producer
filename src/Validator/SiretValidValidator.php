<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\FarmRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SiretValidValidator extends ConstraintValidator
{
    /**
     * @var FarmRepository
     */
    private FarmRepository $farmRepository;
    /**
     * @var HttpClientInterface
     */
    private HttpClientInterface $httpClient;

    public function __construct(FarmRepository $farmRepository, HttpClientInterface $httpClient)
    {
        $this->farmRepository = $farmRepository;
        $this->httpClient = $httpClient;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint SiretValid */

        if (null === $value || '' === $value || $this->farmRepository->count(['siret' => $value]) > 0) {
            return;
        }

        $siretSearch = $this->httpClient->request(
            Request::METHOD_GET,
            "https://entreprise.data.gouv.fr/api/sirene/v3/etablissements/{$value}"
        );

        if ($siretSearch->getStatusCode() === Response::HTTP_NOT_FOUND) {
            $this->context->buildViolation(sprintf('Le numéro Siret "%s" est invalide', $value))
                ->addViolation();
            return;
        }

        $response = $siretSearch->toArray();

        if ($response['etablissement']['etat_administratif'] === 'F') {
            $this->context->buildViolation(
                sprintf('L\'établissement lié au Siret "%s" est déclaré comme fermé', $value)
            )
                ->addViolation();
            return;
        }
    }
}
