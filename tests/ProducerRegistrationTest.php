<?php

declare(strict_types=1);

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ProducerRegistrationTest extends WebTestCase
{
    use MailerAssertionsTrait;

    public function testSuccessfullRegistration(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        // step 1

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate(
                'security_registration_producer_step_one'
            )
        );

        $form = $crawler->filter('form[name=siret]')->form(["siret[siret]" => '34237633200082']);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_registration_producer_step_two');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        // step 2

        $form2 = $client->getCrawler()->filter('form[name=registration_form]')->form(
            [
                'registration_form[email]' => 'blabla@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[lastName]' => 'mylastname',
                'registration_form[firstName]' => 'myfirstname',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ]
        );

        $client->submit($form2);

        self::assertEmailCount(1);

        $email = self::getMailerMessage();

        self::assertEmailHtmlBodyContains($email, 'Veuillez confirmer votre adresse Email');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @dataProvider provideBadRequests
     */
    public function testBadRequest(array $formData, string|array $errorMessages, string $siret): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        // step 1

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate(
                'security_registration_producer_step_one'
            )
        );

        $form = $crawler->filter('form[name=siret]')->form(["siret[siret]" => $siret]);

        $client->submit($form);

        if ($siret === '00000000000000' || $siret === '51171732400012' || $siret === '51236909100024') {
            self::assertResponseStatusCodeSame(Response::HTTP_OK);

            foreach ($errorMessages as $errorMessage) {
                self::assertSelectorTextContains('span.invalid-feedback', $errorMessage);
            }
        } else {
            self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

            $client->followRedirect();

            self::assertRouteSame('security_registration_producer_step_two');

            self::assertResponseStatusCodeSame(Response::HTTP_OK);

            // step 2

            $form2 = $client->getCrawler()->filter('form[name=registration_form]')->form($formData);

            $client->submit($form2);

            self::assertResponseStatusCodeSame(Response::HTTP_OK);

            foreach ($errorMessages as $errorMessage) {
                self::assertSelectorTextContains('span.invalid-feedback', $errorMessage);
            }
        }
    }

    public function provideBadRequests(): Generator
    {
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'pass',
                'registration_form[plainPassword][second]' => 'pass',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            [
                'Le mot de passe doit faire au moins 8 caractères.',
                'Le mot de passe doit contenir des majuscules et des minuscules.',
                'Le mot de passe doit contenir au moins un chiffre.',
                'Le mot de passe doit contenir au moins un caractère spécial.'
            ],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'pass',
                'registration_form[plainPassword][second]' => 'pass2',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Les mots de passe ne correspondent pas.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => '',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Cette valeur ne doit pas être vide.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => '',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Cette valeur ne doit pas être vide.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => '',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Cette valeur ne doit pas être vide.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'fail@mailfake',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Cette valeur n\'est pas une adresse email valide.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'customer@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Cette adresse email est déjà utilisée.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Cette valeur ne doit pas être vide.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '54651',
                'registration_form[farm][description]' => 'my beautiful description'
            ],
            ['Numéro de téléphone invalide.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => ''
            ],
            ['Cette valeur ne doit pas être vide.'],
            '34237633200082'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'petite description'
            ],
            ['Le numéro Siret "00000000000000" est invalide'],
            '00000000000000'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'petit description'
            ],
            ['Ce numéro de siret est déjà enregistré chez nous.'],
            '51171732400012'
        ];
        yield [
            [
                'registration_form[email]' => 'john.doe@email.com',
                'registration_form[plainPassword][first]' => 'superPASS@123',
                'registration_form[plainPassword][second]' => 'superPASS@123',
                'registration_form[firstName]' => 'John',
                'registration_form[lastName]' => 'Doe',
                'registration_form[farm][address][phone]' => '0102030405',
                'registration_form[farm][description]' => 'petit description'
            ],
            ['L\'établissement lié au Siret "51236909100024" est déclaré comme fermé'],
            '51236909100024'
        ];
    }
}
