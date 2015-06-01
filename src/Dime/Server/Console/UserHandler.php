<?php

namespace Dime\Server\Console;

use Dime\Server\Model\User;
use Dime\Server\Hash\SymfonySecurityHasher;
use Webmozart\Console\Api\Args\Args;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Api\IO\IO;

/**
 * UserHandler
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class UserHandler
{
    protected $hasher;

    public function __construct()
    {
        $this->hasher = new SymfonySecurityHasher();
    }

    public function create(Args $args, IO $io, Command $command)
    {
        $io->writeLine("Give a password:");
        $password = trim($io->readLine());

        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $user = new User();
        $user->username = $username;
        $user->salt = $salt;
        $user->password = $this->hasher->make($password, array('salt' => $salt));
        $user->enabled = true;
        $user->save();
    }

    public function password(Args $args, IO $io, Command $command)
    {
        $io->writeLine("Give a username:");
        $username = trim($io->readLine());
        $io->writeLine("Give a password:");
        $password = trim($io->readLine());

        $user = User::where('username', $username)->first();
        if ($user) {
            $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
            $user->salt = $salt;
            $user->password = $this->hasher->make($password, array('salt' => $salt));
            $user->save();
        } else {
            $io->error('Username not found');
        }
    }

    public function enable(Args $args, IO $io, Command $command)
    {
        $user = User::where('username', $args->getArgument('username'))->first();
        if ($user) {
            $user->enabled = true;
            $user->save();
        } else {
            $io->error('Username not found');
        }
    }

    public function disable(Args $args, IO $io, Command $command)
    {
        $user = User::where('username', $args->getArgument('username'))->first();
        if ($user) {
            $user->enabled = false;
            $user->save();
        } else {
            $io->error('Username not found');
        }
    }
}
