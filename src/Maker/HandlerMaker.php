<?php

declare(strict_types=1);

namespace App\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;

class HandlerMaker extends AbstractMaker
{
    private array $formTypes;

    public function __construct(array $formTypes)
    {
        $this->formTypes = $formTypes;
    }

    public static function getCommandName(): string
    {
        return 'maker:handler';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates form handler')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                sprintf(
                    'The class name of the form handler (eg. <fg=yellow>%s</>)',
                    'FooHandler'
                )
            )
            ->addArgument(
                'form-type-class',
                InputArgument::OPTIONAL,
                sprintf(
                    'The class name of the form type to create form handler (eg. <fg=yellow>%s</>',
                    'FooType'
                )
            );

        $inputConfig->setArgumentAsNonInteractive('form-type-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if ($input->getArgument('name') === null) {
            $argument = $command->getDefinition()->getArgument('name');

            $question = new Question($argument->getDescription());

            $responseOfQuestion = $io->askQuestion($question);

            $input->setArgument('name', $responseOfQuestion);
        }

        if ($input->getArgument('form-type-class') === null) {
            $argument = $command->getDefinition()->getArgument('form-type-class');

            $question = new Question($argument->getDescription());

            $question->setAutocompleterValues(array_keys($this->formTypes));

            $responseOfQuestion = $io->askQuestion($question);

            $input->setArgument('form-type-class', $responseOfQuestion);
        }
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        $dependencies->addClassDependency(AbstractType::class, 'form');
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $handlerDetails = $generator->createClassNameDetails($input->getArgument('name'), 'Handler\\', 'Handler');

        $formType = $this->formTypes[$input->getArgument('form-type-class')];

        $generator->generateClass(
            $handlerDetails->getFullName(),
            __DIR__
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'Resources'
            . DIRECTORY_SEPARATOR
            . 'skeleton'
            . DIRECTORY_SEPARATOR
            . 'handler.tpl.php',
            [
                'form_type_full_class_name' => $formType,
                'form_class_name' => $input->getArgument('form-type-class')
            ]
        );

        $generator->writeChanges();

        $io->success('Handler <fg=yellow>' . $input->getArgument('name') . '</> created!');
    }
}
