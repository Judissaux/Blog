<?php

namespace App\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

class DataBaseService
{
    public function __construct(
        private KernelInterface $kernel
    )
    {        
    }

    public function createDatabase(): bool
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'd:s:c' 
        ]);

        $this->run($application,$input);

        $input = new ArrayInput([
            'command' => 'd:s:u',
            '--force' => true
        ]);

        $this->run($application,$input);

        $input = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--append' => true
        ]);

        $this->run($application,$input);

        return Command::SUCCESS;
    }

    private function run(Application $application, ArrayInput $input)
    {
        try{
            $result = $application->run($input);
        }catch(\Exception $e){
            $result = false;
        }

        return $result;
    }
}