## README para ApiCrudGeneratorLaravel

**ApiCrudGeneratorLaravel** es un paquete de Laravel que te ayuda a generar rápidamente código boilerplate para controladores, modelos y rutas para crear APIs RESTful. 

**Características:**

- Genera código para controladores, modelos y rutas RESTful básicas.
- Soporta la creación de tablas en la base de datos (opcional).
- Permite personalizar la generación de código.
- Fácil de usar e instalar.

**Instalación:**

1. Instala el paquete usando Composer:

```bash
composer require yosoymitxel/api-crud-generator-laravel
```

2. Publica los archivos de configuración y migración:

```bash
php artisan vendor:publish --provider="Yosoymitxel\ApiCrudGenerator\CrudGeneratorServiceProvider"
```

**Uso:**

1. Crea un nuevo archivo de comandos:

```bash
php artisan make:command Create{ModelName}Command
```

2. Edita el archivo de comando creado y reemplaza el código con lo siguiente:

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Yosoymitxel\ApiCrudGenerator\ApiCrudGenerator;

class Create{ModelName}Command extends Command
{
    /**
     * The name of the command.
     *
     * @var string
     */
    protected $name = 'make:crud-api:{modelName}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create a CRUD API for the given model name';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modelName = $this->argument('modelName');

        ApiCrudGenerator::generate($modelName, [
            // Opciones de generación
        ]);

        $this->info('CRUD API generated successfully for the model '.$modelName);
    }
}
```

3. Reemplaza `{ModelName}` con el nombre del modelo que deseas generar el CRUD API.
4. Puedes agregar opciones de generación adicionales al array `[]`. Consulta la documentación del paquete para ver todas las opciones disponibles.

**Ejemplo de uso:**

Para generar un CRUD API para el modelo `Post`, ejecuta el siguiente comando:

```bash
php artisan make:crud-api:Post
```

Esto generará los archivos de controlador, modelo y ruta para el modelo `Post`.

**Documentación:**

Para obtener más información sobre el uso y las opciones de ApiCrudGeneratorLaravel, consulta la documentación oficial: [https://github.com/topics/laravel-crud-generator](https://github.com/topics/laravel-crud-generator)

**Contribuciones:**

Si deseas contribuir al desarrollo de ApiCrudGeneratorLaravel, puedes hacerlo en el repositorio de GitHub: [https://github.com/topics/laravel-crud-generator](https://github.com/topics/laravel-crud-generator)

**Nota:**

Este README solo proporciona una descripción general básica del paquete. Se recomienda consultar la documentación oficial para obtener información detallada sobre su uso y configuración.
