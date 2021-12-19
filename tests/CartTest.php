<?php

declare(strict_types=1);

namespace App\Tests;

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

        self::assertEquals(0, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());
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

    public function testAccessDeniedAddToCartForProducer(): void
    {
        $client = static::createAuthenticatedClient("producer@email.com");

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

        self::assertEquals(0, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());

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

        $entityManager->persist($product);

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

        self::assertEquals(1, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());

        $form = $crawler->filter('form[name=cart]')->form(
            [
                'cart[cart][0][quantity]' => 10
            ]
        );

        $client->submit($form);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $crawler = $client->followRedirect();

        self::assertEquals(1, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());

        self::assertEquals(1, $crawler->filter('div > .alert-danger')->count());

        $cart = $crawler
            ->filter('input[id=cart_cart_0_quantity]')
            ->attr('value');

        self::assertEquals(1, $cart);
    }

    public function testIncraseProductQuantity(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Product $product */
        $product = $entityManager->getRepository(Product::class)->getOne();

        $product->setQuantity(1);

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

        self::assertEquals(1, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());

        $link = $crawler->filter('#item_increase_quantity')->link();

        $client->click($link);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('cart_index');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains(
            '.alert-warning',
            'Il ne reste que 1 article pour ce produit. Je ne pouvez pas en ajouter d\'avantage'
        );
    }

    public function testDecreaseProductQuantity(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Product $product */
        $product = $entityManager->getRepository(Product::class)->getOne();

        $product->setQuantity(1);

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

        self::assertEquals(1, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());

        $link = $crawler->filter('#item_decrease_quantity')->link();

        $client->click($link);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('cart_index');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testDecreaseProductQuantityAndRemoveProductIfQuantityEqualZero(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Product $product */
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

        self::assertEquals(1, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());

        $link = $crawler->filter('#item_decrease_quantity')->link();

        $client->click($link);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('cart_index');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('.alert-info', 'Votre panier est vide');

        self::assertSelectorTextContains('.alert-success', 'Produit retiré de votre panier avec succès.');

        self::assertEquals(
            0,
            $client->getCrawler()->filter('form[name=cart] > div > div > div > table > tbody > tr')->count()
        );
    }

    public function testRemoveProduct(): void
    {
        $client = static::createAuthenticatedClient('customer@email.com');

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');

        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        /** @var Product $product */
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

        self::assertEquals(1, $crawler->filter('form[name=cart] > div > div > div > table > tbody > tr')->count());

        $link = $crawler->filter('a.reset-anchor')->link();

        $client->click($link);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('cart_index');

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertSelectorTextContains('.alert-info', 'Votre panier est vide');

        self::assertSelectorTextContains('.alert-success', 'Produit retiré de votre panier avec succès.');

        self::assertEquals(
            0,
            $client->getCrawler()->filter('form[name=cart] > div > div > div > table > tbody > tr')->count()
        );
    }
}
