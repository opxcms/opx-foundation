<?php

namespace Core\Console\Commands;

use Core\Tools\Modules\ModulesLister;
use Illuminate\Console\Command;

class DiscoverModulesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'modules:discover';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modules:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discover all OpxCMS installed modules';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $modules = ModulesLister::discoverModules(app());

        if (empty($modules)) {
            $this->info('Nothing to discover');
        } else {
            foreach ($modules as $module) {
                $this->info('Discovered: ' . $module['module']);
            }
        }

        return 0;
    }
}