<?php

namespace App\Command;


use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Str;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class MakeRouteCommand extends Command
{
    protected static $defaultName = 'make:route';

    private $router;

    public function __construct(RouterInterface $router)
    {
        parent::__construct();

        $this->router = $router;
    }

    protected function configure()
    {

        $this
            ->setDescription('Add route(s) in a controller class and create template file for this route')
            ->addArgument('controller-name', InputArgument::OPTIONAL, sprintf('Name of the controller to create route (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm() . 'Controller')))
            ->addArgument('route-name', InputArgument::OPTIONAL, sprintf('Name of the route to create (e.g. <fg=yellow>%s</>)', Str::asRouteName(Str::getRandomTerm())))
            ->addOption('no-template', 'nt', InputOption::VALUE_NONE, 'Use this option to disable template generation')
            // add un help file
            // ->setHelp(__DIR__.'/../Resources/help/MakeEntity.txt')

        ;

    }

    // call before interract
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output); // TODO: Change the autogenerated stub
    }

    //call before execute
    protected function interact(InputInterface $input, OutputInterface $output)
    {

        parent::interact($input, $output); // TODO: Change the autogenerated stub
        $io = new SymfonyStyle($input, $output);
        //$command=new Command(); //=$this
        if (null === $input->getArgument('controller-name')) {
            $argument = $this->getDefinition()->getArgument('controller-name');

            $question = new Question($argument->getDescription());
            $question->setNormalizer(function ($value) {
                // $value can be null here
                $value = $value ? trim($value) : '';
                if ('Controller' !== substr($value, -10)) {
                    $value .= 'Controller';
                }
                return $value;

            });
            $question->setValidator(function ($answer) {
                //is subclass_of(,'AbstractController')
                if (!class_exists('App\Controller\\' . $answer)) {
                    throw new \RuntimeException('Controller ' . $answer . ' is not defined ! Use php bin/console make:controller');
                }
                return $answer;
            });
            $question->setMaxAttempts(3);
            $input->setArgument('controller-name', $io->askQuestion($question));
        }

        if (null === $input->getArgument('route-name')) {
            $argument = $this->getDefinition()->getArgument('route-name');

            $question = new Question($argument->getDescription());

            $question->setNormalizer(function ($value) {
                // $value can be null here
                $value = $value ? trim($value) : '';
                return Str::asRouteName($value);;
            });

            $question->setValidator(function ($answer) {
                //Test if route exist
                try {
                    $url = $this->router->generate($answer);
                } catch (RouteNotFoundException $e) {
                    // the route is not defined...
                    $url = false;
                }

                if ($url) {
                    //throw new \RuntimeException('Route '.$answer.' already defined in '.$input->getArgument('controller-name').' !');
                    throw new \RuntimeException('Route ' . $answer . ' already defined in Controller !');
                }
                return $answer;
            });
            $question->setMaxAttempts(3);
            $input->setArgument('route-name', $io->askQuestion($question));
        }

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $controllerName = $input->getArgument('controller-name');

        if ($controllerName) {
            $io->note(sprintf('You passed an argument: %s', $controllerName));
        }
        /*
        on controle nos args



                if ($input->getOption('no-template')) {
                    // ...
                }
        */
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        /*
        $filemanager=new FileManager(__DIR__.'/'.$controllerName);
        $generator=new Generator($filemanager,'');
        $r=$generator->dumpFile(__DIR__.'/'.$controllerName);
        dump($r);
        */


        return Command::SUCCESS;
    }

}