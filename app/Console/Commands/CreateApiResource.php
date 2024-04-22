<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class CreateApiResource extends Command
{
    /**
     * The name and signature of the command.
     *
     * @var string
     */
    protected $signature = 'make:crud-api {name : The name of the API resource}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create an API resource (model, controller, and route)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Crea el modelo
        $this->createModel($name);

        // Crea el controlador
        $this->createController($name);

        // Crea la ruta API
        $this->createRoute($name);

        $this->info("API resource '$name' created successfully!");
    }

    private function createModel($name)
    {
        $modelPath = app_path('Models/' . $name . '.php');

        if (file_exists($modelPath)) {
            $this->error("Model '$name' already exists!");
            return;
        }

        // Check for migration existence
        $migrationPath = database_path('migrations/*_create_' . strtolower($name) . '_table.php');

        if (!glob($migrationPath)) {
            $this->warn("No migration found for '$name'. Skipping model creation.");
            return;
        }

        // Extract table structure from migration (assuming basic structure)
        $tableName = null;
        $columns = [];
        foreach (glob($migrationPath) as $migrationFile) {
            $migrationContent = file_get_contents($migrationFile);

            // Obtener el nombre de la tabla
            preg_match("/Schema::create\(\'(\w+)\'/", $migrationContent, $tableMatch);
            $tableName = $tableMatch[1];

            // Obtener los nombres de los campos
            $columns = [];
            preg_match_all('/\$table->(\w+)\((.*?)\)/', $migrationContent, $fieldMatches, PREG_SET_ORDER);

            foreach ($fieldMatches as $match) {
                $fieldName = trim($match[2]);
                if($fieldName && ($match[1] != 'id' || $match[1] != 'timestamps')){
                    $columns[] = str_replace("'",'',$fieldName);
                }
            }

        }

        // Generate model content based on extracted data (basic example)
        $modelContent = "<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class $name extends Model
{
    protected \$table = '$tableName';

    protected \$fillable = ['id',";
        foreach ($columns as $column => $details) {
            $modelContent .= "'$details',";
        }
        $modelContent = rtrim($modelContent, ',') . "];
}
";

        // Save the generated model
        file_put_contents($modelPath, $modelContent);
        $this->info("Model '$name' created based on migration.");
    }


    private function createController($name)
    {
        $controllerSufijo = 'Controller';
        $controllerPath = app_path('Http\\Controllers\\API\\' . $name . $controllerSufijo . '.php');

        if (file_exists($controllerPath)) {
            $this->error("Controller '$name' already exists!");
            return;
        }

        // Generar el controlador
        $this->call('make:controller', ['name' => 'API\\' . $name . $controllerSufijo ]);

        // Obtener el nombre del modelo (asumiendo que el controlador y el modelo tienen el mismo nombre)
        $modelName = str_replace( $controllerSufijo, '', $name);

        // Check for migration existence
        $migrationPath = database_path('migrations/*_create_' . strtolower($name) . '_table.php');

        if (!glob($migrationPath)) {
            $this->warn("No migration found for '$name'. Skipping model creation.");
            return;
        }

        // Extract table structure from migration (assuming basic structure)
        $tableName = null;
        $columns = [];

        foreach (glob($migrationPath) as $migrationFile) {
            $migrationContent = file_get_contents($migrationFile);
            // Obtener los nombres de los campos
            preg_match_all('/\$table->(\w+)\((.*?)\)/', $migrationContent, $fieldMatches, PREG_SET_ORDER);

            foreach ($fieldMatches as $match) {
                $fieldName = trim($match[2]);
                if($fieldName && ($match[1] != 'id' || $match[1] != 'timestamps')){
                    $columns[] = [str_replace("'",'',$fieldName),$match[1]];
                }
            }

        }

        // Generar las funciones CRUD en el controlador
        $crudFunctions = [
            'index', // Listar registros
            'show', // Mostrar un registro específico
            'store', // Crear un nuevo registro
            'update', // Actualizar un registro existente
            'destroy', // Eliminar un registro
        ];

        // Agregar las funciones al controlador
        $controllerContent = file_get_contents($controllerPath);
        $controllerContent = str_replace('}','',$controllerContent);

        foreach ($crudFunctions as $function) {
            $controllerContent .= "\n\n" . $this->generateCrudFunction($function, $modelName,$columns);
        }

        // Guardar el contenido actualizado en el archivo del controlador
        file_put_contents($controllerPath, $controllerContent.'}');

        $this->info("Controller '$name' created with CRUD functions!");
    }

    private function generateCrudFunction($function, $modelName,$fillableFields)
    {
        $validatorRules = [];
        foreach ($fillableFields as  $value) {
            $validatorRules[$value[0]] = 'required' . (($value[1]) ? "|".$value[1] : ''); // Ejemplo: todos los campos son requeridos
        }

        // Generar el código para cada función CRUD
        switch ($function) {
            case 'index':
                // Listar registros
                return "
    public function index()
    {
        \$".strtolower($modelName)." = $modelName::all();
        return response()->json(\$".strtolower($modelName).", 201);
    }
    ";
            case 'show':
                // Mostrar un registro específico
                return "
    public function show(\$id)
    {
        \$record = $modelName::findOrFail(\$id);
        // ... lógica para mostrar el registro específico ...
    }
    ";
            case 'store':
                // Crear un nuevo registro


                return "
    public function store(Request \$request)
    {
        \$validator = Validator::make(\$request->all(), " . var_export($validatorRules, true) . ");

        if (\$validator->fails()) {
            return response()->json(\$validator->errors(), 422);
        }

        \$data = \$request->all();

        \$".strtolower($modelName)." = $modelName::create(\$data);

        return response()->json(\$".strtolower($modelName).", 201);
    }
    ";
            case 'update':
                // Actualizar un registro existente
                return "
    public function update(Request \$request, \$id)
    {
        \$validator = Validator::make(\$request->all(), " . var_export($validatorRules, true) . ");

        if (\$validator->fails()) {
            return response()->json(\$validator->errors(), 422);
        }
        
        \$data = \$request->all();

        \$".strtolower($modelName)." = $modelName::findOrFail(\$id);
        \$".strtolower($modelName)."->update(\$data);

        return response()->json(\$".strtolower($modelName).", 200);
    }
    ";
            case 'destroy':
                // Eliminar un registro
                return "
    public function destroy(\$id)
    {
        $modelName::destroy(\$id);
        return response()->json(['message' => 'Registro eliminado correctamente'], 200);
    }
    ";
            default:
                return "// Función no implementada o inválida";
        }
    }



    private function createRoute($name)
    {
        $routeName = strtolower($name);

        $this->info("Adding route for API resource '$name'");

        Route::apiResource($routeName, 'API\\' . $name . 'Controller');
    }


    private function addRouteToApiFile($variableName)
    {
        $apiFilePath = '\routes\api.php';

        // Verificar si el archivo ya contiene la ruta
        $fileContent = file_get_contents($apiFilePath);
        if (strpos($fileContent, "Route::apiResource('$variableName'") !== false) {
            $this->info("La ruta para '$variableName' ya existe en el archivo.");
            return;
        }

        // Agregar la ruta al final del archivo
        $newRoute = "\n\nRoute::middleware('api')->group(function () {\n";
        $newRoute .= "    Route::apiResource('$variableName', {$variableName}ApiController::class);\n";
        $newRoute .= "});\n";

        file_put_contents($apiFilePath, $fileContent . $newRoute);

        $this->info("Ruta para '$variableName' agregada correctamente al archivo.");
    }


}
