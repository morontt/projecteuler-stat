<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 06.06.16
 * Time: 22:56
 */

namespace MttProjecteuler\Command;

use MttProjecteuler\Model\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesLoad extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('fixtures:load')
            ->setDescription('Load fixtures')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var \Doctrine\DBAL\Connection */
        $db = $this->container['db'];

        $user = new User();
        /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $this->container['security.encoder_factory']->getEncoder($user);

        $user
            ->setUsername('admin')
            ->setEmail('admin@example.org')
            ->setPassword($encoder->encodePassword('test', $user->getSalt()))
        ;

        $db->insert('users', $user->toArray());

        $output->writeln('<info>User <comment>admin</comment> created</info>');
    }
}
