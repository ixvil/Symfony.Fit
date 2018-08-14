<?php
/**
 * Created by PhpStorm.
 * User: ixvil
 * Date: 14/08/2018
 * Time: 22:43
 */

namespace App\Command;


use App\Service\Lesson\LessonManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckLesson extends ContainerAwareCommand
{
    /**
     * @var LessonManager
     */
    private $lessonManager;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('fit:check_lesson')
            // the short description shown while running "php bin/console list"
            ->setDescription('Check lessons.')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Check lessons...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->lessonManager = $this->getContainer()->get('fit.service.lesson.manager');
        $this->lessonManager->checkLessons();
    }
}