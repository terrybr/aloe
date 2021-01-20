<?php

namespace Aloe\Command;

use Aloe\Command;
use Leaf\Str;

class GenerateMigrationCommand extends Command
{
    protected static $defaultName = "g:migration";
    private $description = "Create a new migration file";
    private $help = "Create a new migration file";

    protected function configure()
    {
        $this->setArgument("migration", "required", "migration file name");
    }

    protected function handle()
    {
        $userInput = strtolower(Str::snake(Str::plural($this->argument("migration"))));

        if (strpos($userInput, "create") === false) {
            $userInput = Str::snake("create_$userInput");
        }

        $actualFileName = Str::snake(date("Y_m_d_His") . "_$userInput.php");
        $file = Config::migrations_path($actualFileName);

        touch($file);

        $className = Str::studly($userInput);

        $fileContent = \file_get_contents(__DIR__ . '/stubs/migration.stub');
        $fileContent = str_replace(["ClassName", "tableName"], [$className, str_replace("create_", "", $userInput)], $fileContent);

        file_put_contents($file, $fileContent);

        $this->info(asComment($actualFileName) . " generated successfully");
    }
}
