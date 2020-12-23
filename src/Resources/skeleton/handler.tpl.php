<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $form_type_full_class_name ?>;
use App\HandlerFactory\AbstractHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

/**
* Class <?= $class_name ?>

* @package <?= $namespace ?>

*/
class <?= $class_name ?> extends AbstractHandler
{

    /**
    * @var EntityManagerInterface
    */
    private EntityManagerInterface $entityManager;

    /**
    * @var FlashBagInterface
    */
    private FlashBagInterface $flashBag;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    protected function process($data, array $options): void
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
