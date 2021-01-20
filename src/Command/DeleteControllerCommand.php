<?php

namespace Aloe\Command;

use Aloe\Command;
use Leaf\Str;

class DeleteControllerCommand extends Command
{
    protected static $defaultName = "d:controller";
    private $description = 'Delete a controller';
    private $help = 'Delete a controller';

    protected function configure()
    {
        $this->setArgument("controller", "required", "controller name");
    }

    protected function handle()
    {
        $controller = Str::studly($this->argument("controller"));

        if (!strpos($controller, "Controller")) {
            $controller = str::plural($controller);
            $controller .= "Controller";
        }

        $controllerFile = Config::controllers_path("$controller.php");

        if (!file_exists($controllerFile)) {
            return $this->error("$controller doesn't exist!");
        }

        if (!unlink($controllerFile)) {
            return $this->error("Couldn't delete $controller, you might need to remove it manually.");
        }

        $this->comment("$controller deleted successfully");
    }
}
