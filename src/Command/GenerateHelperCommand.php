<?php

namespace Aloe\Command;

use Aloe\Command;
use Leaf\Str;

class GenerateHelperCommand extends Command
{
    protected static $defaultName = "g:helper";
    private $description = "Create a new helper class";
    private $help = "Create a new helper class";

    protected function configure()
    {
        $this->setArgument("helper", "required", 'helper name');
    }

    protected function handle()
    {
        list($helper, $modelName) = $this->mapNames($this->argument("helper"));

        $file = Config::helpers_path("$helper.php");

        if (file_exists($file)) {
            return $this->error("$helper already exists!");
        }

        if (file_exists(Config::helpers_path(".init"))) {
            unlink(Config::helpers_path(".init"));
        }

        touch($file);

        $fileContent = \file_get_contents(__DIR__ . '/stubs/helper.stub');
        $fileContent = str_replace(['ClassName', 'ModelName'], [$helper, $modelName], $fileContent);
        \file_put_contents($file, $fileContent);

        return $this->comment("$helper generated successfully");
    }

    public function mapNames($helperName)
    {
        $modelName = $helperName;

        if (!strpos($helperName, "Helper")) {
            $helperName .= "Helper";
        } else {
            $modelName = str_replace("Helper", "", $modelName);
        }

        return [$helperName, Str::singular($modelName)];
    }
}
