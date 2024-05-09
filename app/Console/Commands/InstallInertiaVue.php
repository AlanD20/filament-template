<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Laravel\Breeze\Console\InstallCommand;

class InstallInertiaVue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install-inertia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs Inertiajs with vue';

    protected $stubPath = 'stubs/inertia';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $app = $this->choice(
            'JavaScript Framework?',
            ['vue', 'react'],
            0
        );

        $result =  match (strtolower($app)) {
            'vue' => $this->installVue(),
            default => -1,
        };

        if ($result === '') {
            $this->error('unsupported inertia template');

            return Command::FAILURE;
        }

        return $result;
    }

    protected function installVue(): int
    {
        $controllerInertiaPath = 'app/Http/Controllers/Inertia';
        $this->mkdir($controllerInertiaPath);
        $this->createFileFromStub('HomeController.php', 'HomeController.php', $controllerInertiaPath);

        (new Filesystem)->copyDirectory(base_path($this->stubPath . '/resources/js/src'), base_path('resources/js/src'));
        (new Filesystem)->copyDirectory(base_path($this->stubPath . '/resources/views/inertia'), base_path('resources/views/inertia'));

        $this->createFileFromStub('resources/tsconfig.json', 'resources/tsconfig.json');
        $this->createFileFromStub('resources/tsconfig.node.json', 'resources/tsconfig.node.json');

        $installer = invade(new InstallCommand);

        $installer->runCommands(['composer require inertiajs/inertia-laravel=^1.0 tightenco/ziggy=^2.1']);
        $installer->runCommands(['pnpm add -D @inertiajs/vue3@1.0.16 @vitejs/plugin-vue@5.0.4 vue@3.4.27']);
        $installer->runCommands(['pnpm install', 'pnpm run build']);

        return Command::SUCCESS;
    }

    protected function mkdir(string $path): bool
    {
        if (file_exists($path)) {
            $this->error("Directory already exists: '{$path}'");

            return false;
        }

        mkdir(base_path($path), 0777, recursive: true);

        return true;
    }

    protected function createFileFromStub(string $stub, string $filename, string $destination = '', array $template = []): bool
    {
        $path = base_path($destination) . '/' . $filename;

        if (file_exists($path)) {
            $this->error("File already exists: '{$destination}'");

            return false;
        }

        $content = file_get_contents("{$this->stubPath}/{$stub}.stub");

        $content =  str_replace(
            array_keys($template),
            array_values($template),
            $content
        );

        file_put_contents($path, $content);

        return true;
    }
}
