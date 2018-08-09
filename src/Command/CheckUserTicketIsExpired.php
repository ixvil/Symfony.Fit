<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 09/08/2018
 * Time: 02:19
 */

namespace App\Command;


use App\Service\UserTicket\Check;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckUserTicketIsExpired extends ContainerAwareCommand
{
    /**
     * @var Check
     */
    private $check;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('fit:check_user_ticket_expiration')
            // the short description shown while running "php bin/console list"
            ->setDescription('Check and deactivate expired tickets.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Check ...');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->check = $this->getContainer()->get('fit.service.user_ticket.check');
        $this->check->checkExpiration();
    }

}