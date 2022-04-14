<?php

namespace UserAuthorization\Commands;

use Carbon\Carbon;
use Kanata\Commands\Traits\LogoTrait;
use Kanata\Services\Hash;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UserAuthorization\Models\User;

class SeedUsers extends Command
{
    use LogoTrait;

    protected static $defaultName = 'user-auth:seed';

    protected function configure(): void
    {
        $this->setHelp('Seed users to start development.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $output->writeln('');
        $io->info('Kanata - Seed users');
        $output->writeln('');

        User::create([
            'name' => 'Savio',
            'email' => 'savio@savioresende.com',
            'email_verified_at' =>Carbon::now(),
            'password' => Hash::make('password'),
        ]);

        return Command::SUCCESS;
    }
}
