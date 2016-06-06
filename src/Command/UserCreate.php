<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 04.06.16
 * Time: 15:50
 */

namespace MttProjecteuler\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use MttProjecteuler\Model\User;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserCreate extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create user')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = new User();
        /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $this->container['security.encoder_factory']->getEncoder($user);
        $passwordHash = $encoder->encodePassword($password, $user->getSalt());

        $user
            ->setUsername($username)
            ->setPassword($passwordHash)
        ;

        /* @var \Doctrine\DBAL\Connection */
        $db = $this->container['db'];

        try {
            $db->insert('users', $user->toArray());
            $output->writeln('<info>User <comment>' . $username . '</comment> created</info>');
        } catch (UniqueConstraintViolationException $e) {
            $output->writeln('<error>User ' . $username . ' already exists :(</error>');
        }
    }
}
