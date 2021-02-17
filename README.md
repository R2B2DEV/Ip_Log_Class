# Clase Ip Log

_Clase para el registro de las direcciones IP de los clientes visitantes de tu página._

## Comenzando 🚀

### Pre-requisitos 📋

* [ThingEngineer/PHP-MySQLi-Database-Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/blob/master/readme.md) - Para almacenar y extraer datos de la base de datos.
* Crear cuenta en [ipinfo.io](http://ipinfo.io/) para obtener estadisticas sobre tus solicitudes de datos de ip.

### Instalación 🔧

_Pasos a seguir para la instalacion de la clase en tu proyecto:_

1. Instalar [ThingEngineer/PHP-MySQLi-Database-Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/blob/master/readme.md) como especifican en su README.
2. Descargar la clase, o clonarlo en tu proyecto local.
3. Crea las tablas en tu base de datos con el script `ip_log.sql` que se encuentra en este repositorio. Puedes cambiarle el prefijo "tu" de las tablas y agregar el de tu preferencia.
3. Crea la definicion para tu token en tu proyecto: `define("IP_INFO_TOKEN", "tu token");`
5. Añade lo siguiente al archivo PHP principal de tu proyecto: `require '{direccion_carpeta}/ip_log.php';`

_ejemplo:_

```php
<?php
require_once __DIR__ . "src/classes/ip_log/ip_log.php";
```

## Ejemplo ⚙️

_ejemplo de uso de la clase `Ip Log` en un proyecto:_

```php
<?php
// Define tu token como constante.
define("IP_INFO_TOKEN", "token_que_no_existe");
// Asigna la clase a una variable.
$ipLog = new \R2B2\IpLog;
// Guarda los datos relacionados a la direccion IP en la base de datos.
$ipLog->saveLog();
// Deniega el acceso si registra TRUE la columna 'blacklisted' en la tabla de direcciones ip.
$ipLog->denyAccess();
```
_Tomar en cuenta que si se quiere bloquear el acceso, estas 3 líneas deben ser lo segundo que se ejecute en tu script o proyecto, lo primero debería ser [ThingEngineer/PHP-MySQLi-Database-Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/blob/master/readme.md)._
## Licencia 📄

Este proyecto está bajo la Licencia [MIT License](https://github.com/R2B2DEV/Ip_Log_Class/blob/main/LICENSE)