# Clase Ip Log

_Clase para el registro de las direcciones IP de los clientes visitantes de tu pagina._

## Comenzando 🚀

### Pre-requisitos 📋

* [ThingEngineer/PHP-MySQLi-Database-Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/blob/master/readme.md) - Para almacenar y extrar datos de la base de datos.

### Instalación 🔧

_Pasos a seguir para la instalacion de la clase en tu proyecto:_

1. Instalar [ThingEngineer/PHP-MySQLi-Database-Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/blob/master/readme.md) como especifican en su README.
1. Descargar la clase, o clonarlo en tu proyecto local.
2. Crea las tablas en tu base de datos con el script `ip_log.sql` que se encuentra en este repositorio. Puedes cambiarle el prefijo "tu" de las tablas y agregarles el de tu preferencia.
3. Añade lo siguiente al archivo PHP principal de tu proyecto: `require '{direccion_carpeta}/ip_log.php';`

_ejemplo:_

```php
<?php
require_once __DIR__ . "src/classes/ip_log/ip_log.php";
```

## Ejemplo⚙️

_ejemplo de uso de la clase `Ip Log` en un proyecto:_

```php
<?php
// Asigna la clase a una variable.
$ipLog = new \R2B2\IpLog;
// Guarda los datos relacionados a la direccion IP en la base de datos.
$ipLog->saveLog();
// Deniega el acceso si registra TRUE la columna 'blacklisted' en la tabla de direcciones ip.
$ipLog->denyAccess();
```
_Tomar en cuenta que si se quiere bloquear el acceso estas 3 lineas deben ser lo segundo que se ejecute en tu script, lo primero deberia ser [ThingEngineer/PHP-MySQLi-Database-Class](https://github.com/ThingEngineer/PHP-MySQLi-Database-Class/blob/master/readme.md)_
## Licencia 📄

Este proyecto está bajo la Licencia (MIT License) - mira el archivo [LICENSE](https://github.com/R2B2DEV/ip_log/blob/main/LICENSE) para detalles.