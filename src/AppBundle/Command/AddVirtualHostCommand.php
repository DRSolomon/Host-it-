<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AddVirtualHostCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('nhb:host-it:add-virtual-host');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter the name of the server: ');
        $serverName = $helper->ask($input, $output, $question);

        $question = new Question('Please enter path to document root: ');
        $documentRoot = $helper->ask($input, $output, $question);

        if (!$serverName || !$documentRoot) {
            $output->writeln('<error>Please provide needed parameters.</error>');

            return;
        }

        $virtualHostHelper = $this->getContainer()->get('nhb_hostit_virtual_host_helper');

        $virtualHost = $virtualHostHelper->createVirtualHost($documentRoot, $serverName);

        if ($virtualHostHelper->addVirtualHost($virtualHost) === false) {
            foreach ($virtualHostHelper->getErrors() as $error) {
                $output->writeln('<error>'.escapeshellarg($error).'</error>');
            }
            $output->writeln('<info>Virtual Host could not be added.</info>');

            return;
        }

        $output->writeln('<info>Virtual Host '.$virtualHost->getServerName().' added. Please restart apache.</info>');
    }
}
