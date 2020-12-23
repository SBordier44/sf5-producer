<?php

declare(strict_types=1);

namespace App\Handler;

use App\Form\AcceptOrderType;
use App\HandlerFactory\AbstractHandler;
use App\Security\Voter\OrderVoter;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Class AcceptOrderHandler
 * @package App\Handler
 */
class AcceptOrderHandler extends AbstractHandler
{

    /**
     * @var FlashBagInterface
     */
    private FlashBagInterface $flashBag;

    /**
     * @var WorkflowInterface
     */
    private WorkflowInterface $orderStateMachine;

    public function __construct(
        WorkflowInterface $orderStateMachine,
        FlashBagInterface $flashBag,
        ContainerInterface $container
    ) {
        $this->flashBag = $flashBag;

        $this->orderStateMachine = $orderStateMachine;

        $this->setFormFactory($container->get('form.factory'));
    }

    protected function process($data, array $options): void
    {
        $this->orderStateMachine->apply($data, OrderVoter::ACCEPT);

        $this->flashBag->add('success', 'La commande a été acceptée avec succès.');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => AcceptOrderType::class
            ]
        );
    }
}
