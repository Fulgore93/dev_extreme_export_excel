# dev extreme 
 
## Introducción
Proyecto laravel 10 Devextreme datagrid y librerías para descargas de excel y pdf a través de la devextreme (archivos en public/js/devextreme).

## Instalación
Para instalar esto, debes seguir las siguientes indicaciones
- Tener php 8.1+ y composer acorde a la versión de php
- composer install
- cp .env.example .env
- php artisan key:generate
- php artisan migrate
- php artisan db:seed 
- - por defecto se poblarán las tablas con 1000 datos, puedes modificar esta cantidad en database/seeders/DatabaseSeeder.php

# Ejemplos de uso
Para ver los ejemplos de uso, puedes hacer lo siguiente:
- Ir a la ruta / y navegar entre los enlaces (opciones en el selector del header)
- - Todo junto: Ejemplo de tabla simple con el código en la vista blade.
- - En javascript: Ejemplo de tabla simple con el código en javascript siendo llamado en la vista blade.
- - Con agrupacion: Ejemplo de tabla simple con método de agrupar columnas de la devextreme
- - Con maestro detalle: Ejemplo de tabla con maestro-detalle, permite descarga de excel de maestro detalle

# Referencias
- https://laravel.com/docs/10.x/releases

## Bitácora

Fecha | Descripción | Acciones
| :-- | :-: | :-:
18-05-2022 18:11 | instalación de proyecto | Ejecutar composer install, php artisan migrate:fresh y php artisan db:seed