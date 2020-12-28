<?php

namespace App\Tests;

use App\Entity\Farm;
use App\Entity\Producer;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CartTest extends WebTestCase
{
    use AuthenticationTrait;

    public function testSuccessfullAddToCart(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $entityManager->getRepository(Product::class)->getOne();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'cart_add',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->request(Request::METHOD_GET, $router->generate('cart_index'));

        self::assertEquals(1, $crawler->filter('tbody > tr')->count());

        $form = $crawler->filter('form[name=cart]')->form(
            [
                'cart[cart][0][quantity]' => 0
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->followRedirect();

        self::assertEquals(0, $crawler->filter('tbody > tr')->count());
    }

    public function testFailedAddToCartIfUserIsNotLogged(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $entityManager->getRepository(Product::class)->getOne();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'cart_add',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testSuccessfullShowCart(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('cart_index'));

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testRedirectToLoginIfUserIsNotLoggedForShowCart(): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $client->request(Request::METHOD_GET, $router->generate('cart_index'));

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    public function testAccessDeniedAddToCart(): void
    {
        $client = static::createAuthenticatedClient("customer@email.com");

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        $product = $entityManager->getRepository(Product::class)->getOne();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                "cart_add",
                [
                    "id" => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $producer = $entityManager->getRepository(Producer::class)->findOneByEmail("producer@email.com");

        $farm = $entityManager->getRepository(Farm::class)->findOneByProducer($producer);

        $product = $entityManager->getRepository(Product::class)->getOneBy(
            [
                'farm' => $farm->getId()
            ]
        );

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                "cart_add",
                [
                    "id" => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testFailedAddStockOutProductToCart(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Product $product */
        $product = $entityManager->getRepository(Product::class)->getOne();

        $product->setQuantity(0);

        $entityManager->flush();

        $entityManager->refresh($product);

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'cart_add',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->request(Request::METHOD_GET, $router->generate('cart_index'));

        self::assertEquals(0, $crawler->filter('tbody > tr')->count());

        self::assertEquals(1, $crawler->filter('div > .alert-warning')->count());
    }

    public function testFailureIfTheRequestedQuantityOfProductIsGreeterThanQuantityInStock(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        $product = $entityManager->getRepository(Product::class)->getOne();

        $product->setQuantity(5);

        $entityManager->flush();

        $client->request(
            Request::METHOD_GET,
            $router->generate(
                'cart_add',
                [
                    'id' => $product->getId()
                ]
            )
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->request(Request::METHOD_GET, $router->generate('cart_index'));

        self::assertEquals(1, $crawler->filter('tbody > tr')->count());

        $form = $crawler->filter('form[name=cart]')->form(
            [
                'cart[cart][0][quantity]' => 10
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->followRedirect();

        self::assertEquals(1, $crawler->filter('tbody > tr')->count());

        self::assertEquals(1, $crawler->filter('div > .alert-warning')->count());

        $cart = $crawler
            ->filter('input[id=cart_cart_0_quantity]')
            ->attr('value');

        self::assertEquals(1, $cart);
    }
}
