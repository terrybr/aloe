<?php

namespace Aloe\Command;

use Aloe\Command;
use Illuminate\Support\Str;

class DeleteFactoryCommand extends Command
{
    public $name = "d:factory";
    public $description = "Delete a model factory";
    public $help = "Delete a model factory";

    public function config()
    {
        $this->setArgument("factory", "required", "factory name");
    }

    public function handle()
    {
        $factory = Str::studly(Str::singular($this->argument("factory")));

        if (!strpos($factory, "Factory")) {
            $factory .= "Factory";
        }

        $file = Config::factories_path("$factory.php");

        if (!file_exists($file)) {
            return $this->error("$factory doesn't exists");
        }

        unlink($file);

        $this->comment("$factory deleted successfully");
    }
}
