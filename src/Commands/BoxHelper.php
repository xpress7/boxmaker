<?php

namespace Xpress7\BoxMaker\Commands;

use File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\ConsoleOutput;
use Artisan;

class BoxHelper
{

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->output = new ConsoleOutput();
        $this->base_path = 'app/Box/';
    }


    /*
     * Creates Model*/
    public function createModel($box_name, $model_name)
    {
        $box_name = Str::ucfirst($box_name);
        $model_name = Str::ucfirst($model_name);

        $box_model_path = $this->base_path . $box_name . '/Models/';
        $model_file_name = $model_name . '.php';

        try {
            if (!File::exists($box_model_path . $model_file_name)) {
                $this->createDirectory($box_model_path);

                // This will replace the ModelName in stub file, with our model_name
                $model_template_temp = str_replace(
                    ['{{box_name}}'],
                    [$box_name],
                    $this->getStub('Model')
                );

                $controller_template = str_replace(
                    ['{{model_name}}'],
                    [$model_name],
                    $model_template_temp
                );


                $model_created = $this->createFile($box_model_path . $model_file_name, $modelTemplate);
                if ($model_created) {
                    $this->output->writeln('Model Created Successfully');
                }
            } else {
                $this->output->writeln($model_name . ' already exists');
            }
        } catch (\Exception $exception) {
            $this->output->writeln($exception);
        }
    }


    /*
     * This function creates a controller
     *
     * */

    public function createController($box_name)
    {
        //$this->output->writeln($this->base_path);
        //dd("in controller");
        $box_name = Str::ucfirst($box_name);
        try {
            $box_path = $this->base_path . $box_name;
            // path for the controllers folder in the box
            $box_controller_path = $box_path . '/Controllers/';
            $controller_name = $box_name . 'Controller';
            // Now creating Controllers directoruy
            $this->createDirectory($box_controller_path);

            // Creating Api Controller.
            $this->createControllerFile($box_name, $controller_name, 'API' );
            // Creating Web Controller.
            $this->createControllerFile($box_name, $controller_name, 'Web' );


        } catch (\Exception $exception) {
            $this->output->writeln($exception);
        }
    }


    /*
     * This function creates the controller files
     * This will take 3 arguments. Box Name, Controller Name, and Route.
     * Route is Either API or Web
     * */
    public function createControllerFile($box_name, $controller_name, $route)
    {
        $route = Str::upper($route);
        $box_controller_path = $this->base_path.$box_name . '/Controllers/';
        // Route is Web or API
        $route_controller_path = $box_controller_path.$route.'/';

        // name of the  controller
        $controller_name = $controller_name;
        // name of the controller file
        $controller_file = $controller_name. '.php';

        if (!File::exists($route_controller_path . $controller_file)) {
            // We create Route controller path, if it doesn't exist
            $this->createDirectory($route_controller_path);

            // This will replace the controllername in stub file, with our box_name
            $controller_template_temp = str_replace(
                ['{{box_name}}'],
                [$box_name],
                $this->getStub('Controller')
            );

            $controller_template = str_replace(
                ['{{route}}'],
                [$route],
                $controller_template_temp
            );

            $controller_created = $this->createFile($route_controller_path . $controller_file, $controller_template);;
            if ($controller_created) {
                $this->output->writeln($controller_name . ' Created');
            }
        } else {
            $this->output->writeln($controller_name . ' already exists');
        }
    }


    /*
     * This function will create routes folder and files
     *
     * */
    public function createRoutes($box_name)
    {
        $route_name = Str::plural(Str::lower($box_name));

        $routes_path = $this->base_path . $box_name . '/Routes/';
        $this->createDirectory($routes_path);

        $api_template_temp = str_replace(
            ['{{box_name}}'],
            [$box_name],
            $this->getStub('/routes/api')
        );

        $api_template = str_replace(
            ['{{route_name}}'],
            [$route_name],
            $api_template_temp
        );


        $this->createFile($routes_path . 'api.php', $api_template);
        $this->createFile($routes_path . 'web.php', '<?php // Place web routes here');
    }

    /*
     * This will create the service provider for the class, add routes in boot() method
     * Register the service provider in config/app.php file
     * */

    public function createProvider($box_name)
    {
        $box_providers_path = $this->base_path . $box_name . '/Providers/';
        $provider_name = $box_name . 'ServiceProvider';
        $provider_file = $box_name . 'ServiceProvider.php';

        try {
            // Checks whether the service provider already exists or not
            if (!File::exists($box_providers_path . $provider_file)) {
                $this->createDirectory($box_providers_path);
//                Artisan::call('make:provider', ['name' => $provider_name]);
                $provider_template = str_replace(
                    ['{{box_name}}'],
                    [$box_name],
                    $this->getStub('ServiceProvider')
                );

                $provider_created = $this->createFile($box_providers_path . $provider_file, $provider_template);
                if ($provider_created) {
                    $this->output->writeln($provider_name . ' Created');
                }
            } else {
                $this->output->writeln($provider_name . ' already exists');
            }
        } catch (\Exception $exception) {
            $this->output->writeln($exception);
        }
    }


    /*
     * This fucntion creates transformer
     * */

    public function createTransformer($box_name)
    {
        $box_transformer_path = $this->base_path . $box_name . '/Transformers/';
        $transformer_name = $box_name . 'Transformer';
        $transformer_file_name = $box_name . 'Transformer.php';

        try {
            if (!File::exists($box_transformer_path . $transformer_file_name)) {
                $this->createDirectory($box_transformer_path);

                $transformer_template = str_replace(
                    ['{{box_name}}'],
                    [$box_name],
                    $this->getStub('Transformer')
                );

                $transformer_created = $this->createFile($box_transformer_path . $transformer_file_name, $transformer_template);
                if ($transformer_created) {
                    $this->output->writeln($transformer_file_name . " Created");
                }
            } else {
                $this->output->writeln($transformer_name . ' already exists');
            }
        } catch (\Exception $exception) {
            $this->output->writeln($exception);
        }
    }


    /*
     * This will generate the database stub
     * */
    public function createDatabase($box_name)
    {
        // We will convert the box_name to lower case, and add plural
        $table_name = Str::plural(Str::lower($box_name));

        $box_database_path = $this->base_path . $box_name . '/Database/';
        $box_migrations_path = $this->base_path . $box_name . '/Database/migrations/';
        $migration_name = 'create_' . $table_name . '_table';

        try {
            // First we will create Database folder, if it doesn't exist
//            $this->createDirectory($box_database_path);
//            $this->createDirectory($box_migrations_path);
            Artisan::call('make:migration', ['name' => $migration_name]);
            $this->output->writeln(Artisan::output());
//            File::move();
        } catch (\Exception $exception) {
            $this->output->writeln($exception);
        }

    }


    /*
     * Creates Requests folder and Request stub
     * */
    public function createRequest($box_name, $request_name)
    {
        $box_requests_path = $this->base_path . $box_name . '/Requests/';
        $request_file_name = $request_name . '.php';

        try {
            if (!File::exists($box_requests_path . $request_file_name)) {
                $this->createDirectory($box_requests_path);

                $request_template_temp = str_replace(
                    ['{{box_name}}'],
                    [$box_name],
                    $this->getStub('Request')
                );

                $request_template = str_replace(
                    ['{{request_name}}'],
                    [$request_name],
                    $request_template_temp
                );

                $request_created = $this->createFile($box_requests_path . $request_file_name, $request_template);
                if ($request_created) {
                    $this->output->writeln($request_file_name . " Created");
                }
            } else {
                $this->output->writeln($request_file_name . ' already exists');
            }
        } catch (\Exception $exception) {
            $this->output->writeln($exception);
        }
    }



    /*
     * This function creates a new Directory
     * First it will check whether the directory exists or not
     * */

    public function createDirectory($path)
    {
        if (!File::exists($path)) {
            try {
                $created = File::makeDirectory($path);
                if ($created) {
                    $this->output->writeln($path . ' Created');
                }
                return $created;
            } catch (\Exception $exception) {
                return $this->output->writeln($exception);
            }
        } else {
            return $this->output->writeln($path . ' already exists');
        }
    }


    /*
     * Gets the stub file
     * */

    protected function getStub($type)
    {
        return File::get(__DIR__ . '/../stubs/' . $type . '.stub');
    }


    /*
     * Creates A new file, first checks if the file exist
     * @return void
     * */

    public function createFile($path, $content)
    {
        if (!File::exists($path)) {
            try {
                $created = File::put($path, $content);
                if ($created) {
                    $this->output->writeln($path . ' Created');
                }
            } catch (\Exception $exception) {
                $this->output->writeln($exception);
            }
        } else {
            $this->output->writeln($path . 'file already exists');
        }
    }
}
