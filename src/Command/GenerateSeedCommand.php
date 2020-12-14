<?php

namespace Aloe\Command;

use Aloe\Command;
use Illuminate\Support\Str;

class GenerateSeedCommand extends Command
{
	public $name = "g:seed";
	public $description = "Create a new seed file";
	public $help = "Create a new seed file";

	public function config()
	{
		$this
			->setArgument('model', "required", "model name")
			->setArgument("name", "optional", "seed name")
			->setOption("factory", "f", "none", "Create a factory for seeder");
	}

	public function handle()
	{
		$modelName = Str::studly(Str::singular($this->argument("model")));
		$seedName = $this->argument("name") ?? $modelName;
		$factory = $this->option("factory");
		$className = Str::studly(Str::plural($seedName));

		if (!strpos($seedName, "Seeder")) {
			$className .= "Seeder";
		}

		$file = Config::seeds_path("$className.php");

		if (file_exists($file)) {
			return $this->writeln("<error>$seedName already exists</error>");
		}

		touch($file);
		
		$fileContent = \file_get_contents(__DIR__ . '/stubs/seed.stub');

		if ($factory && !file_exists(Config::factories_path("{$modelName}Factory.php"))) {
			$fileContent = str_replace(
				[
					"// You can directly create db records",
					"
use App\Models\ModelName;",
					'

        // $entity = new ModelName();
        // $entity->field = \'value\';
        // $entity->save();

        // or

        // ModelName::create([
        //    "field" => "value"
        // ]);'
				],
				["(new ModelNameFactory)->create(5)->save();", "", ""],
				$fileContent
			);
		}

		$fileContent = str_replace(
			["ClassName", "ModelName", "entity"],
			[$className, $modelName, Str::lower($modelName)],
			$fileContent
		);
		
		file_put_contents($file, $fileContent);
		
		$this->comment("$className generated successfully");

		if ($factory && !file_exists(Config::factories_path("{$modelName}Factory.php"))) {
			$this->comment(shell_exec("php leaf g:factory $modelName"));
		}
	}
}
