<?php

declare(strict_types=1);

namespace App\HandlerFactory;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractHandler implements HandlerInterface
{
    private FormFactoryInterface $formFactory;

    private FormInterface $form;

    public function setFormFactory(FormFactoryInterface $formFactory): void
    {
        $this->formFactory = $formFactory;
    }

    abstract protected function process($data, array $options): void;

    protected function configure(OptionsResolver $resolver): void
    {
    }

    public function handle(Request $request, $data = null, array $options = []): bool
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired('form_type');

        $resolver->setDefault('form_options', []);

        $this->configure($resolver);

        $options = $resolver->resolve($options);

        $this->form = $this->formFactory
            ->create($options['form_type'], $data, $options['form_options'])
            ->handleRequest($request);

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $this->process($data, $options);

            return true;
        }
        return false;
    }

    public function createView(): FormView
    {
        return $this->form->createView();
    }
}
