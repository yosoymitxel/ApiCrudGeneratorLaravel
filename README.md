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
git clone https://github.com/yosoymitxel/ApiCrudGeneratorLaravel.git
```

**Uso:**

Para generar un CRUD API para el modelo `Clientes`, ejecuta el siguiente comando:

```bash
php artisan make:migration create_clientes_table
php artisan migrate --seed
php artisan make:crud-api Clientes
php artisan route:list
```

Esto generará los archivos de controlador, modelo y ruta para el modelo `Clientes`.

**Contribuciones:**

Si deseas contribuir al desarrollo de ApiCrudGeneratorLaravel, puedes hacerlo en el repositorio de GitHub: [https://github.com/topics/laravel-crud-generator](https://github.com/topics/laravel-crud-generator)

**Nota:**

Este README solo proporciona una descripción general básica del paquete. Se recomienda consultar la documentación oficial para obtener información detallada sobre su uso y configuración.
