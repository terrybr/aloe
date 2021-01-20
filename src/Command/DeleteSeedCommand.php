<?php

namespace Aloe\Command;

use Aloe\Command;
use Leaf\Str;

class DeleteSeedCommand extends Command
{
    protected static $defaultName = "d:seed";
    private $description = "Delete a model seeder";
    private $help = "Delete a model seeder";

    protected function configure()
    {
        $this->setArgument("seed", "required", "seeder name");
    }

    protected function handle()
    {
        $seeder = Str::studly($this->argument("seed"));

        if (!strpos($seeder, "Seeder")) {
            $seeder = str::plural($seeder);
            $seeder .= "Seeder";
        }

        $seederFile = Config::seeds_path("$seeder.php");

        if (!file_exists($seederFile)) {
            return $this->error("$seeder doesn't exist!");
        }

        if (!unlink($seederFile)) {
            return $this->error("Couldn't delete $seeder, you might need to remove it manually.");
        }

        $this->comment("$seeder deleted successfully");
    }
}
