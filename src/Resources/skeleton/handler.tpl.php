<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $form_type_full_class_name ?>;
use App\HandlerFactory\AbstractHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class <?= $class_name ?> extends AbstractHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FlashBagInterface $flashBag)
    {
    }

    protected function process(mixed $data, array $options): void
    {
        $this->entityManager->flush();

        $this->flashBag->add('success', 'success message');
    }

    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'form_type' => <?= $form_class_name ?>::class
            ]
        );
    }
}
