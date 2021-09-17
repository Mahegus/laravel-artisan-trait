<?php

namespace matt127127\TraitCommand\Commands;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class CreateTraitCommand extends Command
{

    /**
     * Name and signature of Command.
     * name
     * @var string
     */
    protected $name = 'make:trait';

    protected $signature = 'make:trait {name}';

    protected $description = 'Generates a Trait in the App\\Traits folder, folder is created if it doesn\'t exist';

    public function __construct()
    {
        parent::__construct();
    }

    protected function getArguments()
    {
        return [
            ['trait', InputArgument::REQUIRED, 'The name of the trait']
        ];
    }

    /**
     * getTraitName
     *
     * @return string
     */
    private function getTraitName(): string
    {
        return Str::studly($this->argument('trait'));
    }

    /**
     * getDestinationFilePath
     *
     * @return string
     */
    protected function getDestinationFilePath(): string
    {
        return app_path()."/Traits".'/'. $this->getTraitName() . '.php';
    }

    /**
     * getTraitNameWithoutNamespace
     *
     * @return string
     */
    private function getTraitNameWithoutNamespace(): string
    {
        return class_basename($this->getTraitName());
    }

    /**
     * getDefaultNamespace
     *
     * @return string
     */
    public function getDefaultNamespace() : string
    {
        return "App\\Traits";
    }

    /**
     * getStubFilePath
     *
     * @return void
     */
    protected function getStubFilePath(): string
    {
        return __DIR__.'/stubs/traits.stub';
    }

    /**
     * getTemplateContents
     *
     * @return string
     */
    protected function getTemplateContents(): string
    {
        $template = file_get_contents($this->getStubFilePath());

        $replaces = [
            'CLASS_NAMESPACE' => $this->getClassNamespace(),
            'CLASS' => $this->getTraitNameWithoutNamespace()
        ];

        foreach ($replaces as $search => $replace) {
            $template = str_replace('$' . strtoupper($search) . '$', $replace, $template);
        }

        return $template;

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());


        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }

        $contents = $this->getTemplateContents();

        try {

            $filesystem = new Filesystem();

            if ($filesystem->exists($path)) {
                throw new \Exception('File already exists!');
            }

            $filesystem->put($path, $contents);
            $this->info("Created : {$path}");

        } catch (\Exception $e) {

            $this->error($e->getMessage());

            return E_ERROR;
        }

        return 0;

    }

    protected function getClassNamespace(): string
    {

        $extra = str_replace($this->getTraitNameWithoutNamespace(), '', $this->getTraitName());

        $extra = str_replace('/', '\\', $extra);

        $namespace =  $this->getDefaultNamespace();

        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }

}
