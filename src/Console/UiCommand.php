<?php

namespace Canvas\Console;

use Illuminate\Console\Command;

class UiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'canvas:ui { --force : Overwrite existing views by default }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Public the frontend APIs for Canvas UI';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Routes and controller...
        $this->exportBackend();

        $this->info('Installation complete.');
    }

    /**
     * Export the backend controller and routes.
     *
     * @return void
     */
    protected function exportBackend()
    {
        file_put_contents(
            app_path('Http/Controllers/CanvasUiController.php'),
            str_replace(
                '{{namespace}}',
                $this->laravel->getNamespace(),
                file_get_contents(dirname(__DIR__, 2).'/resources/stubs/controllers/CanvasUiController.stub')
            )
        );

        file_put_contents(
            base_path('routes/api.php'),
            file_get_contents(dirname(__DIR__, 2).'/resources/stubs/routes/api.stub'),
            FILE_APPEND
        );

        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(dirname(__DIR__, 2).'/resources/stubs/routes/web.stub'),
            FILE_APPEND
        );
    }

}
