script-hack-helper
==================

Un script de ayuda para deshackear sitios en PHP

El script inicial lo encontré en http://blog.aw-snap.info/p/simple-script-to-find-base64decode-in.html

Bajo la necesidad de deshackear sitios míos y de amigos, 
lo modifiqué para que fuese más fácil deshackear sitios infectados.

No se si será bueno o malo, yo de linux lo justito, pero a mi me funciona.

Como hacer

Poner script en el directorio raiz de la web (subir por FTP)

Configurar correctamente "$current_dir" en el caso de que no funcione   __DIR__

Primera prueba http://tuweb.com/archivo.php


Configurar cadenas de texto a buscar dentro de los archivos: 
$str_to_find[]='base64_decode';
$str_to_find[]='otra cadena';

Configurar los archivos que sabes ya tienen esas cadenas de forma nativa (comunes)
$common[]='com_content/controller.php';
$common[]='com_mailto/controller.php';
$common[]='com_user/controller.php';

Configurar los archivos se borrarán en el modo delete: 
$deletefiles[] = '/Auth/OpenID/';
$deletefiles[] = '/js/tokenizephp.js';
$deletefiles[] = '/beez/';
$deletefiles[] = '/w.php';
$deletefiles[] = '/pp1.php';

variables GET para lanzar el script:
days (número de días de modificación del archivo)  int
delete (borra los archivos del array)   1/0
commons (mostrar/ocultar archivos comunes)   1/0
manual (mostrar/ocultar archivos manual check) 1/0

ejemplos: 
muestra archivos modificados en los últimos 4 días:
http://tuweb.com/archivo.php?days=4
borra los archivos configurados en el script
http://tuweb.com/archivo.php?delete=1
no mostrar ni archivos comunes ni de checkeo manual
http://tuweb.com/archivo.php?commons=0&manual=0  


Fin
Espero sirva de ayuda a mi me ayuda bastante
Seguro que es mejorable
Espero que alguien lo mejore y lo comparta…
@ciroartigot para #joomlaIO



