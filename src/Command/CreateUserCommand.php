<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loicb
 * Date: 24/05/18
 * Time: 17:34
 */

namespace App\Command;

use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class CreateUserCommand extends Command
{
    /**
     * @var \App\Manager\UserManager
     */
    private $userManager;

    /**
     * CreateUserCommand constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;

        parent::__construct();
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('app:user:create')
            ->setDescription('Create a user.')
            ->setDefinition([
                new InputArgument('email', InputArgument::REQUIRED, 'The email'),
                new InputArgument('password', InputArgument::REQUIRED, 'The password'),
                new InputOption('super-admin', null, InputOption::VALUE_NONE, 'Set the user as super admin'),
                new InputOption('inactive', null, InputOption::VALUE_NONE, 'Set the user as inactive'),
            ])
            ->setHelp(<<<EOT
The <info>app:user:create</info> command creates a user:

  <info>php app/console app:user:create example@example.com</info>

This interactive shell will ask you for a password.

You can alternatively password as the second argument:

  <info>php app/console app:user:create example@example.com mypassword</info>

You can create a super admin via the super-admin flag:

  <info>php app/console app:user:create example@example.com --super-admin</info>

You can create an inactive user (will not be able to log in):

  <info>php app/console app:user:create example@example.com --inactive</info>

EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('email');
        $password = $input->getArgument('password');
        $inactive = $input->getOption('inactive');
        $superadmin = $input->getOption('super-admin');

        $user = $this->userManager->create();
        $user->setUsername($username)
            ->setPlainPassword($password);

        if ($inactive) {
            $user->setActive(false);
        }

        if ($superadmin) {
            $user->setRoles([User::ROLE_SUPER_ADMIN]);
        }

        $this->userManager->save($user);
        $output->writeln(sprintf('Created user <comment>%s</comment>', $username));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('email')) {
            $question = new Question('Please choose a username:');
            $question->setValidator(function ($username) {
                if ('' == trim($username)) {
                    throw new \Exception('Username can not be empty');
                }

                return $username;
            });

            $username = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('email', $username);
        }

        if (!$input->getArgument('password')) {
            $question = new Question('Please choose a password:');
            $question
                ->setHidden(true)
                ->setValidator(function ($password) {
                    if ('' == trim($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                });

            $password = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument('password', $password);
        }
    }
}
