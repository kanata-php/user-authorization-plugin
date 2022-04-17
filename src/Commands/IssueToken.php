<?php

namespace UserAuthorization\Commands;

use Carbon\Carbon;
use Exception;
use Kanata\Commands\Traits\LogoTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UserAuthorization\Models\User;
use UserAuthorization\Repositories\TokenRepository;

class IssueToken extends Command
{
    use LogoTrait;

    protected static $defaultName = 'token:issue';

    protected function configure(): void
    {
        $this
            ->setHelp('This command issues a token for a user.')
            ->setDefinition(
                new InputDefinition([
                    new InputOption('name', null, InputOption::VALUE_REQUIRED, 'The name for the token.'),
                    new InputOption('email', null, InputOption::VALUE_REQUIRED, 'The user email.'),
                    new InputOption('expire', null, InputOption::VALUE_OPTIONAL, 'Expire date. (e.g.: --expire="1971-01-27 00:00")'),
                    new InputOption('domain', null, InputOption::VALUE_OPTIONAL, 'Domain allowed for token. (e.g.: --domain="kanata.com")'),
                    new InputOption('no-ssl', null, InputOption::VALUE_NONE, 'Protocol do skip ssl. (e.g.: --no-ssl)'),
                ])
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $name = $input->getOption('name');
        $email = $input->getOption('email');
        $expire = $input->getOption('expire');
        $domain = $input->getOption('domain');
        $noSsl = $input->getOption('no-ssl');

        $error = false;
        if (null === $name) {
            $error = true;
            $io->error('Name is required. (e.g.: --email="some name")');
        }

        if (null === $email) {
            $error = true;
            $io->error('Email is required. (e.g.: --email=doe@kanata.com)');
        } else {
            $user = User::byEmail($email)->first();
            if (null === $user) {
                $error = true;
                $io->error('User not found for email: ' . $email);
            }
        }

        if ($error) {
            return Command::FAILURE;
        }

        $protocol = 'https://';
        if ($noSsl) {
            $protocol = 'http://';
        }

        $data = [
            'name' => $name,
            'user_id' => $user->id,
        ];

        if (null !== $expire) {
            $data['expire_at'] = Carbon::parse($expire);
        }

        if (null !== $domain) {
            $data['aud'] = $domain;
            $data['aud_protocol'] = $protocol;
        }

        try {
            $tokenRecord = (new TokenRepository)->createToken($data);
        } catch (Exception $e) {
            $io->error('Failed to issue token: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $io->write('Token: ' . $tokenRecord->token . PHP_EOL);
        return Command::SUCCESS;
    }
}
