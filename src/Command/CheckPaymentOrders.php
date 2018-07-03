<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 03/07/2018
 * Time: 00:25
 */

namespace App\Command;

use App\Service\UserTicket\Check;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckPaymentOrders extends ContainerAwareCommand
{
    /**
     * @var Check
     */
    private $check;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('fit:check_payment_orders')
            // the short description shown while running "php bin/console list"
            ->setDescription('Check new payment orders.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Check new payment orders...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->check = $this->getContainer()->get('fit.service.user_ticket.check');
        $this->check->check();
    }
}