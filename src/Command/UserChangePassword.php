<?php

namespace MttProjecteuler\Command;

use MttProjecteuler\Utils\Salt;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UserChangePassword extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('user:change-password')
            ->setDescription('Update user password')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        /* @var \MttProjecteuler\Database\Repository $repository */
        $repository = $this->container['pe_database.repository'];
        $user = $repository->findUserByUsername($username);

        if (!$user) {
            $output->writeln('<error>User "' . $username . '" not found :(</error>');
            exit(1);
        }

        /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $this->container['security.encoder_factory']->getEncoder($user);

        $salt = Salt::generate();
        $passwordHash = $encoder->encodePassword($password, $salt);

        $output->writeln($salt);
        $output->writeln($passwordHash);

        $data = [
            'salt' => $salt,
            'password_hash' => $passwordHash,
        ];

        /* @var \Doctrine\DBAL\Connection */
        $db = $this->container['db'];

        $db->update('users', $data, ['id' => $user->getId()]);
        $output->writeln('<info>User <comment>' . $username . '</comment> updated</info>');
    }
}
