<?php

namespace Benblub\Ftg\Bundle\Maker;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * @author Benjamin Knecht
 */
final class MakeFunctionalTest extends AbstractMaker implements MakerInterface
{
    /** @var ManagerRegistry */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public static function getCommandName(): string
    {
        return 'make:ftg';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
        $command
            ->setDescription('Creates a Functional Test for a Resource')
            ->addArgument('entity', InputArgument::OPTIONAL, 'Entity class to create a FunctionalTest for')
            ->addArgument('role', InputArgument::OPTIONAL, 'role for the auth User eg user, admin or whatever')
            ->addOption('deny', 'd', InputOption::VALUE_OPTIONAL, 'Test Deny CRUD for Role xy')
        ;

        $inputConfig->setArgumentAsNonInteractive('entity');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command): void
    {
        if ($input->getArgument('entity')) {
            return;
        }

        $argument = $command->getDefinition()->getArgument('entity');
        $entity = $io->choice($argument->getDescription(), $this->entityChoices());

        $input->setArgument('entity', $entity);
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        $class = $input->getArgument('entity');

        if (!\class_exists($class)) {
            $class = $generator->createClassNameDetails($class, 'Entity\\')->getFullName();
        }

        if (!\class_exists($class)) {
            throw new RuntimeCommandException(\sprintf('Entity "%s" not found.', $input->getArgument('entity')));
        }

        $role = $this->getRole($input->getArgument('role'));
        $getRoleAsName = $this->getRoleAsName($input->getArgument('role'));
        $deny = $input->getOption('deny');

        if ($deny === 'deny') {
            $getRoleAsName = 'Deny' . $getRoleAsName;
        }

        $entity = new \ReflectionClass($class);
        $factory = $generator->createClassNameDetails(
            $entity->getShortName(),
            'Tests\\Functional',
            $getRoleAsName . 'Test'
        );

        $repository = new \ReflectionClass($this->managerRegistry->getRepository($entity->getName()));

        if (0 !== \mb_strpos($repository->getName(), $generator->getRootNamespace())) {
            // not using a custom repository
            $repository = null;
        }

        $generator->generateClass(
            $factory->getFullName(),
            __DIR__.'/../Resources/skeleton/' . $this->loadTemplate($deny),
            [
                'entity' => $entity,
                'entityProperties' => $entity->getDefaultProperties(),
                'entityShorName' => $entity->getShortName(),
                'entityShorNameLowercase' => strtolower($entity->getShortName()) . 's',
                'role' => $role,
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Open your new FunctionalTest and finish it.',
            'Find the documentation at https://github.com/benblub/ftg',
        ]);
    }

    public function getRole(string $role)
    {
        if ($role) {
            return 'ROLE_' . strtoupper($role);
        }

        return 'ROLE_USER';
    }

    public function getRoleAsName(string $role)
    {
        if ($role) {
            return 'As' . ucfirst($role);
        }

        return 'AsUser';
    }

    public function configureDependencies(DependencyBuilder $dependencies): void
    {
        // noop
    }

    private function entityChoices(): array
    {
        $choices = [];

        foreach ($this->managerRegistry->getManagers() as $manager) {
            foreach ($manager->getMetadataFactory()->getAllMetadata() as $metadata) {
                $choices[] = $metadata->getName();
            }
        }

        \sort($choices);

        return $choices;
    }

    /**
     * Currently there are 2 Template
     * 1. With a given ROLE there isset a Bearer Token to all Request
     * 2. As Anymous there is no auth and the Function names differ
     */
    private function loadTemplate(string $deny)
    {
        if ($deny === 'deny') {
            return 'DenyFunctionalTest.tpl.php';
        }

        return 'FunctionalTest.tpl.php';
    }
}
