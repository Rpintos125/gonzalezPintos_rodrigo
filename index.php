<?php
require_once 'includes/conexion.php';

$mensaje_tarea = '';
$error_tarea = '';
$mensaje_nota = '';
$archivo_notas = 'data/notas_rapidas.txt';

// 1. Procesar formulario de nueva tarea (MySQL con consultas preparadas)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_tarea'])) {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? '';

    $prioridades_validas = ['Alta', 'Media', 'Baja'];

    if ($titulo !== '' && $descripcion !== '' && in_array($prioridad, $prioridades_validas, true)) {
        $sql = "INSERT INTO tareas (titulo, descripcion, prioridad) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $titulo, $descripcion, $prioridad);
            if (mysqli_stmt_execute($stmt)) {
                $mensaje_tarea = "La tarea se guardó correctamente.";
            } else {
                $error_tarea = "Error al guardar la tarea: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_tarea = "Error en la consulta: " . mysqli_error($conexion);
        }
    } else {
        $error_tarea = "Por favor, completa todos los campos correctamente.";
    }
}

// 2. Procesar formulario de Notas rápidas / Comentarios (Archivo de texto)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_nota'])) {
    $nota = trim($_POST['nota'] ?? '');

    if ($nota !== '') {
        if (!is_dir('data')) {
            mkdir('data', 0777, true);
        }

        $fecha_hora = date('d/m/Y H:i:s');
        $nota_limpia = str_replace(["\r\n", "\r", "\n"], ' ', $nota);
        $nueva_linea = "[$fecha_hora] - $nota_limpia" . PHP_EOL;

        file_put_contents($archivo_notas, $nueva_linea, FILE_APPEND | LOCK_EX);
        $mensaje_nota = "Nota guardada en el historial.";
    }
}

// 3. Leer historial de notas desde data/notas_rapidas.txt
$historial_notas = [];
if (file_exists($archivo_notas)) {
    $lineas = file($archivo_notas, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lineas !== false) {
        $historial_notas = array_reverse($lineas);
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App de Tareas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-zinc-950 text-zinc-200 min-h-screen py-12 px-4 antialiased selection:bg-zinc-800 selection:text-white">

    <div class="max-w-2xl mx-auto space-y-10">

        <!-- Encabezado y botón hacia tareas.php -->
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-zinc-800/80">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-zinc-100">Gestor de Tareas</h1>
                <p class="text-xs text-zinc-500 mt-1">Organiza tus actividades y registra notas rápidas</p>
            </div>
            <a href="tareas.php"
                class="inline-flex items-center justify-center gap-2 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 text-xs font-medium px-4 py-2.5 rounded-lg transition">
                <span>Ver tareas guardadas</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </a>
        </header>

        <!-- Sección 1: Formulario para agregar tarea -->
        <section class="bg-zinc-900/50 p-6 sm:p-7 rounded-xl border border-zinc-800/80">
            <h2 class="text-base font-medium text-zinc-100 mb-1">Nueva Tarea</h2>
            <p class="text-xs text-zinc-500 mb-6">Completa los datos para registrar una tarea en la base de datos.</p>

            <?php if ($mensaje_tarea !== ''): ?>
                <div class="mb-6 p-3.5 rounded-lg bg-emerald-950/40 border border-emerald-900/60 text-emerald-400 text-xs">
                    <?= htmlspecialchars($mensaje_tarea) ?>
                </div>
            <?php endif; ?>

            <?php if ($error_tarea !== ''): ?>
                <div class="mb-6 p-3.5 rounded-lg bg-rose-950/40 border border-rose-900/60 text-rose-400 text-xs">
                    <?= htmlspecialchars($error_tarea) ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST" class="space-y-4">
                <div>
                    <label for="titulo" class="block text-xs font-medium text-zinc-400 mb-1.5">Título</label>
                    <input type="text" id="titulo" name="titulo" required maxlength="100"
                        placeholder="Ej: Preparar entrega de proyecto"
                        class="w-full rounded-lg bg-zinc-950 border border-zinc-800 px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-zinc-600 transition">
                </div>

                <div>
                    <label for="descripcion" class="block text-xs font-medium text-zinc-400 mb-1.5">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" required
                        placeholder="Detalla los pasos o recordatorios de la tarea..."
                        class="w-full rounded-lg bg-zinc-950 border border-zinc-800 px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-zinc-600 transition"></textarea>
                </div>

                <div>
                    <label for="prioridad" class="block text-xs font-medium text-zinc-400 mb-1.5">Prioridad</label>
                    <select id="prioridad" name="prioridad" required
                        class="w-full rounded-lg bg-zinc-950 border border-zinc-800 px-3.5 py-2 text-sm text-zinc-100 focus:outline-none focus:border-zinc-600 transition">
                        <option value="Alta">Alta</option>
                        <option value="Media" selected>Media</option>
                        <option value="Baja">Baja</option>
                    </select>
                </div>

                <div class="pt-3 flex flex-col sm:flex-row sm:items-center gap-3">
                    <button type="submit" name="guardar_tarea"
                        class="bg-zinc-100 hover:bg-white text-zinc-950 font-medium px-5 py-2 rounded-lg text-xs transition">
                        Agregar Tarea
                    </button>
                    <a href="tareas.php"
                        class="text-center text-xs text-zinc-400 hover:text-zinc-200 px-3 py-2 transition">
                        Ir a la lista de tareas &rarr;
                    </a>
                </div>
            </form>
        </section>

        <!-- Sección 2: Notas rápidas / Comentarios -->
        <section class="bg-zinc-900/50 p-6 sm:p-7 rounded-xl border border-zinc-800/80">
            <h2 class="text-base font-medium text-zinc-100 mb-1">Notas rápidas / Comentarios</h2>
            <p class="text-xs text-zinc-500 mb-6">Registro con fecha y hora de Argentina en <code class="bg-zinc-950 border border-zinc-800 px-1.5 py-0.5 rounded text-zinc-400">data/notas_rapidas.txt</code>.</p>

            <?php if ($mensaje_nota !== ''): ?>
                <div class="mb-6 p-3.5 rounded-lg bg-emerald-950/40 border border-emerald-900/60 text-emerald-400 text-xs">
                    <?= htmlspecialchars($mensaje_nota) ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST" class="flex flex-col sm:flex-row gap-2.5 mb-8">
                <input type="text" name="nota" required
                    placeholder="Escribe una nota rápida o comentario..."
                    class="flex-1 rounded-lg bg-zinc-950 border border-zinc-800 px-3.5 py-2 text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none focus:border-zinc-600 transition">
                <button type="submit" name="guardar_nota"
                    class="bg-zinc-800 hover:bg-zinc-700 text-zinc-100 border border-zinc-700 font-medium px-4 py-2 rounded-lg text-xs transition whitespace-nowrap">
                    Guardar Nota
                </button>
            </form>

            <!-- Historial de notas -->
            <div>
                <h3 class="text-[11px] font-medium uppercase tracking-widest text-zinc-500 mb-3">Historial</h3>

                <?php if (empty($historial_notas)): ?>
                    <div class="text-center py-8 px-4 rounded-lg bg-zinc-950/60 border border-dashed border-zinc-800 text-zinc-600 text-xs">
                        Todavía no hay notas registradas.
                    </div>
                <?php else: ?>
                    <ul class="space-y-2">
                        <?php foreach ($historial_notas as $item_nota): ?>
                            <li class="p-3 rounded-lg bg-zinc-950 border border-zinc-800/80 text-xs text-zinc-300 font-mono break-words">
                                <?= htmlspecialchars($item_nota) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </section>

    </div>

</body>

</html>
