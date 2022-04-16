<?php

namespace UserAuthorization\Commands;

use Carbon\Carbon;
use Kanata\Commands\Traits\LogoTrait;
use Kanata\Services\Hash;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UserAuthorization\Models\User;

class SeedUsers extends Command
{
    use LogoTrait;

    protected static $defaultName = 'user-auth:seed';

    protected function configure(): void
    {
        $this
            ->setHelp('Seed users to start development.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('name', null, InputOption::VALUE_REQUIRED, 'The user name.'),
                    new InputOption('email', null, InputOption::VALUE_REQUIRED, 'The user email.'),
                    new InputOption('password', null, InputOption::VALUE_REQUIRED, 'The user password.'),
                    new InputOption('email-verified', null, InputOption::VALUE_NONE, 'If the email is already verified.'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $output->writeln('');
        $io->info('Kanata - Seed users');
        $output->writeln('');

        $name = $input->getOption('name');
        $email = $input->getOption('email');
        $password = $input->getOption('password');
        $emailVerified = $input->getOption('email-verified');

        $error = false;
        if (null === $name) {
            $error = true;
            $io->error('Name option (e.g.: --name="John Doe") is required.');
        }

        if (null === $email) {
            $error = true;
            $io->error('Email option (e.g.: --email=doe@kanata.com) is required.');
        }

        if (null === $password) {
            $error = true;
            $io->error('Password option (e.g.: --password=secret) is required.');
        }

        if ($error) {
            return Command::FAILURE;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ];

        if ($emailVerified) {
            $data['email_verified_at'] = Carbon::now();
        }

        User::create($data);

        return Command::SUCCESS;
    }
}
