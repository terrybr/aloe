<?php

namespace Aloe\Command;

use Aloe\Command;
use Leaf\Str;

class GenerateModelCommand extends Command
{
    protected static $defaultName = "g:model";
    private $description = "Create a new model class";
    private $help = "Create a new model class";

    protected function configure()
    {
        $this
            ->setArgument('model', "required", 'model file name')
            ->setOption("migration", "m", "none", 'Create a migration for model');
    }

    protected function handle()
    {
        $model = Str::singular(Str::studly($this->argument("model")));
        $className = $model;

        if (strpos($model, "/") && strpos($model, "/") !== 0) {
            list($dirname, $className) = explode("/", $model);
        }

        $file = Config::models_path("$model.php");

        if (file_exists($file)) {
            return $this->error("Model already exists");
        }

        $fileContent = \file_get_contents(__DIR__ . '/stubs/model.stub');
        $fileContent = str_replace("ClassName", $className, $fileContent);

        if (!is_dir(dirname($file))) mkdir(dirname($file));

        file_put_contents($file, $fileContent);

        $this->info(asComment($model) . " model generated");

        if ($this->option('migration')) {
            $migration = Str::snake(Str::plural($model));
            $process = $this->runProcess("php aloe g:migration $migration");

            $this->info(
                $process === 0 ?
                    asComment($migration) . " migration generated" :
                    asError("Couldn't generate migration")
            );
        }
    }
}
