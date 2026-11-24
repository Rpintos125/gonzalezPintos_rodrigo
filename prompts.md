# Registro de Prompts - Desarrollo de `app_tareas`

Este documento recopila de forma cronológica todas las consultas (*prompts*) utilizadas durante el desarrollo de la aplicación de gestión de tareas y notas rápidas en PHP, MySQL y Tailwind CSS.

---

## 1. Conexión a la base de datos (`includes/conexion.php`)
**Objetivo:** Crear el archivo de conexión a la base de datos MySQL `app_tareas`.

> Necesito crear un archivo conexion.php que se conecte a la base de datos app_tareas. esto dentro de la carpeta includes.

---

## 2. Configuración de zona horaria
**Objetivo:** Configurar la zona horaria de Argentina al inicio de `includes/conexion.php`.

> agregale al principio la configuración de zona horaria de Argentina (America/Argentina/Buenos_Aires)

---

## 3. Página principal (`index.php`)
**Objetivo:** Crear la vista principal con Tailwind CSS por CDN, incluyendo el alta de tareas en MySQL mediante consultas preparadas, la navegación a `tareas.php` y el sistema de notas rápidas persistidas en `data/notas_rapidas.txt`.

> Estoy haciendo una app de tareas en PHP, mi coneccion esta en includes/conexion.php. Necesito crear el index.php con diseño moderno usando Tailwind CSS sin instalarlo, por CDN. 
> Debe tener:
> 1. Un formulario para agregar una tarea (campos: titulo, descripcion y un select prioridad con Alta, Media y Baja) que haga el INSERT en la tabla tareas de MySQL usando consultas preparadas.
> 2. Un botón que cambie a una nueva página llamada tareas.php donde se verán las tareas guardadas en la base de datos.
> 3. Debajo, una sección de 'Notas rápidas / Comentarios' con un formulario de texto simple: al enviarlo, PHP debe tomar la fecha y hora actual de Argentina, guardar la nota con su fecha al final del archivo notas_rapidas.txt en la carpeta data y leer ese archivo para mostrar el historial de notas en pantalla.

---

## 4. Listado y eliminación de tareas (`tareas.php`)
**Objetivo:** Crear la página de visualización de tareas registradas en MySQL (`SELECT`), eliminación por `id` (`DELETE`) y botón de regreso al inicio.

> Crea la nueva página tareas.php con el mismo diseño en Tailwind CSS por CDN y usando includes/conexion.php. TIENE QUE:
> Consultar con SELECT todos los registros de la tabla tareas y mostrarlos en tarjetas o en una tabla prolija.
> Tener botón 'Eliminar' en cada tarea que borre ese registro de la base de datos por su id y recargue la lista.
> Un botón para volver a index.php.

---

## 5. Rediseño visual (Oscuro y minimalista)
**Objetivo:** Actualizar la paleta de colores y estilos de `index.php` y `tareas.php` hacia una interfaz oscura y minimalista.

> cambia los colores, tiene que ser más oscuro y minimalista.

---

## 6. Documentación de consultas (`prompts.md`)
**Objetivo:** Generar este archivo de registro en la raíz del proyecto con todos los prompts de la sesión.

> Generá un archivo llamado prompts.md en la raíz de mi proyecto que recopile de forma prolija y ordenada todas las consultas (prompts) que te hice en esta sesión para desarrollar la aplicación.
