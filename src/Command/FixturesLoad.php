<?php
/**
 * Created by PhpStorm.
 * User: morontt
 * Date: 06.06.16
 * Time: 22:56
 */

namespace MttProjecteuler\Command;

use Faker\Factory;
use MttProjecteuler\Model\Lang;
use MttProjecteuler\Model\Solution;
use MttProjecteuler\Model\User;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixturesLoad extends BaseCommand
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var array
     */
    protected $userIds = [];

    /**
     * @var array
     */
    protected $langIds = [];

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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->db = $this->container['db'];
        $this->output = $output;

        $this->faker = Factory::create();
        $this->faker->seed(54321);

        $this->createUsers();
        $this->createLanguages();
        $this->createSolutions();
    }

    protected function createUsers()
    {
        $user = new User();
        /* @var \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface $encoder */
        $encoder = $this->container['security.encoder_factory']->getEncoder($user);

        $user
            ->setUsername('admin')
            ->setEmail('admin@example.org')
            ->setEmailHash(md5(strtolower('admin@example.org')))
            ->setPassword($encoder->encodePassword('test', $user->getSalt()))
        ;

        $this->db->insert('users', $user->toArray());
        $this->userIds[] = $this->db->lastInsertId();
        $this->output->writeln('<info>User <comment>admin</comment> created</info>');

        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $email = $this->faker->email;
            $user
                ->setUsername($this->faker->userName)
                ->setEmail($email)
                ->setEmailHash(md5(strtolower($email)))
                ->setPassword($encoder->encodePassword('test', $user->getSalt()))
            ;
            $this->db->insert('users', $user->toArray());
            $this->userIds[] = $this->db->lastInsertId();
        }

        $this->output->writeln('<info>Others users created</info>');
    }

    protected function createLanguages()
    {
        $languages = [
            ['C', 'gcc 4.8.2', 'c'],
            ['Java', '1.8.0_91', 'java'],
            ['ECMAScript', 'Node.js 4.4.3', 'js'],
            ['PHP', '5.5.9', 'php'],
            ['Clojure', '1.8.0', 'clojure'],
            ['Go', '1.6.0', 'go'],
            ['Python', '2.7.6', 'python'],
            ['COBOL', '9.1.1', 'cobol'],
            ['Lua', '5.2', 'lua'],
            ['Ruby', '2.3.1', 'rb'],
        ];

        foreach ($languages as $lang) {
            $entity = new Lang();
            $entity
                ->setName($lang[0])
                ->setComment($lang[1])
                ->setLexer($lang[2])
                ->setCreatedBy((int)$this->userIds[$this->faker->numberBetween(0, count($this->userIds) - 1)])
            ;
            $this->db->insert('languages', $entity->toArray());
            $this->langIds[] = $this->db->lastInsertId();
        }

        $this->output->writeln('<info>Languages created</info>');
    }

    protected function createSolutions()
    {
        for ($i = 0; $i < 450; $i++) {
            $entity = new Solution();
            $entity
                ->setProblemNumber($this->faker->numberBetween(1, 42))
                ->setCreatedBy((int)$this->userIds[$this->faker->numberBetween(0, count($this->userIds) - 1)])
                ->setLangId((int)$this->langIds[$this->faker->numberBetween(0, count($this->langIds) - 1)])
                ->setExecutionTime($this->faker->numberBetween(1000, 80000) * 0.001)
                ->setDeviationTime($this->faker->numberBetween(100, 8000) * 0.001)
                ->setPublic(true)
            ;
            $this->db->insert('solutions', $entity->toArray());
        }

        $this->output->writeln('<info>Solutions created</info>');
    }
}
