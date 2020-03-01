<?php

namespace ArtemSchander\L5Modular\Console;

use ArtemSchander\L5Modular\Traits\HasModuleOption;
use Illuminate\Foundation\Console\MailMakeCommand as BaseMailMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MailMakeCommand extends BaseMailMakeCommand
{
    use HasModuleOption;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:module:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new mail class in a module';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $this->initModuleOption();

        return $this->module ? parent::handle() : false;
    }

    /**
     * Write the Markdown template for the mailable.
     *
     * @return void
     */
    protected function writeMarkdownTemplate()
    {
        $path = app_path().'/Modules/'.Str::studly($this->option('module')).'/views/'.str_replace('.', '/', $this->option('markdown')).'.blade.php';

        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $base_class = new ReflectionClass(BaseMailMakeCommand::class);
        
        $base_class_path = dirname($base_class->getFileName());

        $this->files->put($path, file_get_contents($base_class_path.'/stubs/markdown.stub'));
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Modules\\'.Str::studly($this->module).'\Mail';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();

        $options[] = ['module', null, InputOption::VALUE_OPTIONAL, 'Generate a mailable in a certain module'];

        return $options;
    }
}
