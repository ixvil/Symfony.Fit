<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 11/09/2018
 * Time: 09:33
 */

namespace App\Command;

use App\Entity\UserTicket;
use App\Service\UserTicket\GetList;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetUserTickets extends ContainerAwareCommand
{

    /**
     * @var GetList
     */
    private $getList;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('fit:get_user_tickets')
            // the short description shown while running "php bin/console list"
            ->setDescription('Get user tickets.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Get User Tickets ...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getList = $this->getContainer()->get('fit.service.user_ticket.get_list');
        $userTickets = $this->getList->get();

        /** @var UserTicket[] $userTicketsArray */
        $userTicketsArray = $userTickets->toArray();

        usort(
            $userTicketsArray, function (UserTicket $a, UserTicket $b) {
            return ($a->getExpirationDate() < $b->getExpirationDate()) ? -1 : 1;
        }
        );

        echo 'Телефон';
        echo "\t\t";
        echo 'Бонусы';
        echo "\t";
        echo 'Заканчивается';
        echo "\t";
        echo 'Ост уроков';
        echo "\t";
        echo 'Активен';
        echo "\t";
        echo 'Имя';
        echo "\t\t\t\t";
        echo 'Абонемент';
        echo "\t";

        echo "\n";


        foreach ($userTicketsArray as $userTicket) {
            if ($userTicket->getTicketPlan()->getType()->getId() == 3) {
                continue;
            }
            if ($userTicket->getExpirationDate() < new \DateTime('04.09.2018')) {
                continue;
            }
            if ($userTicket->getExpirationDate() > new \DateTime('16.09.2018')) {
                continue;
            }
            if ($this->userTicketSum($userTicket->getUser()->getUserTickets()) > $userTicket->getLessonsExpires()) {
                continue;
            }

            echo $userTicket->getUser()->getPhone();
            echo "\t";
            echo $userTicket->getUser()->getBonusBalance();
            echo "\t";
            echo $userTicket->getExpirationDate()->format("d.m.Y");
            echo "\t";
            echo $userTicket->getLessonsExpires();
            echo "\t\t";
            echo $userTicket->getIsActive();
            echo "\t";
            echo $userTicket->getUser()->getName();
            echo "\t\t\t";
            echo $userTicket->getTicketPlan()->getName();
            echo "\n";
        }
    }

    /**
     * @param Collection $userTickets
     * @param int[]      $types
     *
     * @return int
     */
    public function userTicketSum(Collection $userTickets, array $types = [1, 2, 4]): int
    {
        $sum = 0;
        foreach ($userTickets as $userTicket) {
            if (!in_array($userTicket->getTicketPlan()->getType()->getId(), $types)) {
                continue;
            }
            if (!$userTicket->getIsActive()) {
                continue;
            }
            $sum += $userTicket->getLessonsExpires();
        }

        return $sum;

    }

}