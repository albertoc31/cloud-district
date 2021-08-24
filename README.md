# cloud-district
Prueba técnica para cloud district

**Consideraciones:**
1) El requisto de guardar en bbdd el precio con impuestos implica que la relación entre Product y Tax sea "One to Many", lo cual va a significar problemas de escalado tratándose de una aplicación _eCommerce_ en la que se manejan distintos impuestos para un mismo producto (incluso más de uno) según la ubicación del comprador.
2) La creación de producto requiere que se envíe a la API un tipo impositivo correcto. Además de la validación que se aplique en el proceso de creación, deberíamos tener en el frontend o cliente de la API un paso previo de solicitar la lista de tipos impositivos que se puedan aplicar (en un frontend html se mostrarían en un select, p.ej.). Es por ello que hemos previsto que en el json de la petición se pase un id valido de tipo impositivo (y no el porcentaje), por razones de consistencia de datos.
3) La autorización por token solo nos importa en este ejemplo para el metodo POST. A efectos de testing solo hemos creado un usuario con el api_token "admintoken". En una aplicación más desarrollada deberíamos contemplafr un sistema de caducidad de token o implementar un endpoint de login y una autenticación con JWT token (por ejemplo) como la implementada en este otro ejercicio (Symfony 3.4):  https://github.com/albertoc31/apiTaskManager

**Despliegue y Testing:**
1) Los tests funcionales requieren una bbdd de test y se necesita además cargar las fixtures. Los test unitarios (Helpers y Validators) no requieren de nada en especial.
2) Una vez realizado el despliegue en el entorno de staging, habrá que lanzar estos comandos:

   bin/console --env=test doctrine:database:create
   
   bin/console --env=test doctrine:migrations:migrate
   
   bin/console --env=test doctrine:fixtures:load
   
   Y finalmente lanzar los tests con el comando bin/phpunit
   
**Notas:**
1) Hay alguna incompatibilidad entre la versión de Symfony 5.3 y PHP 7.4 por el uso de psr/container[1.1.1].
https://stackoverflow.com/questions/67172779/error-while-installing-symfony-declaration-dependencyinjection-psr-php-what-t

   El despliegue en un contenedor Docker ha sido satisfactorio con PHP 8