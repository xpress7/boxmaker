<?php

namespace Xpress7\BoxMaker\Commands;

use Illuminate\Console\Command;
use File;
use Illuminate\Support\Str;
use Xpress7\BoxMaker\Commands\BoxHelper;

class CreateBox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'box:create {box_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates A New Box';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Initiate the box helper
        $helper = new BoxHelper();
        // First creates the Box folder in app, if it doesn't exist
        $helper->createDirectory('app/Box');

        // First convert the first letter of box to upper case
        $box_name = Str::ucfirst($this->argument('box_name'));
        $root_path = 'app/Box/';
        $box_path = $root_path . $box_name;

        try {
            $box_created = $helper->createDirectory($box_path);
            // Create Controller
            $helper->createController($box_name, $box_name . 'Controller');
            // Create Model
//            $this->createModel($box_name);
            $helper->createModel($box_name, $box_name);
            // Create Transformer
            $helper->createTransformer($box_name);
            // Create routes folder
            $helper->createRoutes($box_name);
            // Create database
            $helper->createDatabase($box_name);
            // Create Service Provider, Register the service provider in config/app.php file
            $helper->createProvider($box_name);

            if ($box_created) {
                // This will display the result text on terminal
                $this->info('Box Created Successfully');
            }

        } catch (\Exception $exception) {
            return $exception;
        }
    }
}
