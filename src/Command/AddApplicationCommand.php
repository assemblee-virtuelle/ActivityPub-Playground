<?php

namespace App\Command;

use App\Entity\Actor\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddApplicationCommand extends Command
{
    protected static $defaultName = 'app:add-application';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED);
    }

    public function generateRandomString(int $length): string
    {
        return str_pad(dechex(random_int(0, 0xFFFFFFFFFFFF)), $length, '0', STR_PAD_LEFT);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $apiKey = $this->generateRandomString(16);

        $application = new Application();
        $application
            ->setUsername($username)
            ->setName('Incoming webhook')
            ->setApiKey($apiKey);

        $this->em->persist($application);
        $this->em->flush();

        $output->writeln([
            "Generated a new application with username : $username",
            "API key for this application : $apiKey"
        ]);
    }
}
