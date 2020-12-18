<?php

namespace App\Tests;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ProductTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfullGetProductList(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_index')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testSuccessfullProductUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_update', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=product]')->form(
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @dataProvider provideBadRequests
     * @param array $formData
     * @param string $errorMessage
     */
    public function testFailedProductUpdate(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_update', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=product]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function testSuccessfullProductCreate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        $form = $crawler->filter('form[name=product]')->form(
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    /**
     * @param array $formData
     * @param string $errorMessage
     * @dataProvider provideBadRequests
     */
    public function testFailedProductCreate(array $formData, string $errorMessage): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        $form = $crawler->filter('form[name=product]')->form($formData);

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', $errorMessage);
    }

    public function testSuccessfullProductStockUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_stock', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=stock]')->form(
            [
                'stock[quantity]' => 25
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testFailedProductStockUpdate(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $crawler = $client->request(
            Request::METHOD_GET,
            $router->generate('product_stock', ['id' => $product->getId()])
        );

        $form = $crawler->filter('form[name=stock]')->form(
            [
                'stock[quantity]' => -24
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('span.form-error-message', 'Cette valeur doit être supérieure ou égale à 0.');
    }

    public function testSuccessfullProductDelete(): void
    {
        $client = static::createAuthenticatedClient('producer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_delete', ['id' => $product->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function provideBadRequests(): Generator
    {
        yield [
            [
                'product[name]' => '',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => '',
                'product[price][unitPrice]' => 100,
                'product[price][vat]' => 2.1
            ],
            'Cette valeur ne doit pas être vide.'
        ];
        yield [
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => '',
                'product[price][vat]' => 2.1
            ],
            'Cette valeur n\'est pas valide.'
        ];
        yield [
            [
                'product[name]' => 'Produit laitier',
                'product[description]' => 'Super Produit de ma ferme biologique',
                'product[price][unitPrice]' => -10,
                'product[price][vat]' => 2.1
            ],
            'Cette valeur doit être supérieure ou égale à 0.'
        ];
    }

    public function testAccessDeniedProductCreate(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProductUpdate(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_update',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProductDelete(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_delete',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAccessDeniedProducts(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_index'
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRedirectToLoginIfNotLoggedUserInProductCreateAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate('product_create')
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testRedirectToLoginIfNotLoggedUserInProductUpdateAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_update',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testRedirectToLoginIfNotLoggedUserInProductDeleteAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        /** @var EntityManagerInterface $manager */
        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $manager->getRepository(Product::class)->findOneBy([]);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_delete',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testRedirectToLoginIfNotLoggedUserInProductsAction(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'product_index'
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }
}
