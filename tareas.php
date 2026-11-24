<?php
require_once 'includes/conexion.php';

// Procesar eliminación de tarea por su ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_id'])) {
    $id_eliminar = (int) $_POST['eliminar_id'];

    if ($id_eliminar > 0) {
        $sql_delete = "DELETE FROM tareas WHERE id = ?";
        $stmt = mysqli_prepare($conexion, $sql_delete);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_eliminar);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    // Recargar la lista
    header("Location: tareas.php");
    exit;
}

// Consultar todos los registros de la tabla tareas
$tareas = [];
$sql_select = "SELECT * FROM tareas ORDER BY id DESC";
$resultado = mysqli_query($conexion, $sql_select);

if ($resultado) {
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $tareas[] = $fila;
    }
    mysqli_free_result($resultado);
}

// Función auxiliar para asignar colores oscuros/minimalistas según la prioridad
function clasePrioridad(string $prioridad): string {
    switch (strtolower($prioridad)) {
        case 'alta':
            return 'bg-rose-950/50 text-rose-400 border-rose-900/60';
        case 'media':
            return 'bg-amber-950/50 text-amber-400 border-amber-900/60';
        case 'baja':
            return 'bg-emerald-950/50 text-emerald-400 border-emerald-900/60';
        default:
            return 'bg-zinc-900 text-zinc-400 border-zinc-800';
    }
}
?>
<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tareas Guardadas - App de Tareas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-zinc-950 text-zinc-200 min-h-screen py-12 px-4 antialiased selection:bg-zinc-800 selection:text-white">

    <div class="max-w-2xl mx-auto space-y-10">

        <!-- Encabezado y botón para volver a index.php -->
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-zinc-800/80">
            <div>
                <h1 class="text-xl font-semibold tracking-tight text-zinc-100">Tareas Guardadas</h1>
                <p class="text-xs text-zinc-500 mt-1">Listado completo de tareas registradas en la base de datos</p>
            </div>
            <a href="index.php"
                class="inline-flex items-center justify-center gap-2 bg-zinc-900 hover:bg-zinc-800 text-zinc-200 border border-zinc-800 text-xs font-medium px-4 py-2.5 rounded-lg transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                </svg>
                <span>Volver al inicio</span>
            </a>
        </header>

        <!-- Listado de tareas -->
        <section class="space-y-3">
            <?php if (empty($tareas)): ?>
                <div class="bg-zinc-900/50 p-10 rounded-xl border border-zinc-800/80 text-center space-y-4">
                    <p class="text-zinc-500 text-xs">No hay tareas guardadas en la base de datos todavía.</p>
                    <a href="index.php"
                        class="inline-flex items-center gap-2 bg-zinc-100 hover:bg-white text-zinc-950 text-xs font-medium px-4 py-2 rounded-lg transition">
                        Agregar primera tarea
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-3">
                    <?php foreach ($tareas as $tarea): ?>
                        <?php $prioridad = $tarea['prioridad'] ?? $tarea['propiedad'] ?? 'Media'; ?>
                        <article class="bg-zinc-900/50 p-5 rounded-xl border border-zinc-800/80 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 hover:border-zinc-700 transition">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap items-center gap-2.5">
                                    <span class="text-xs font-mono text-zinc-600">#<?= (int) $tarea['id'] ?></span>
                                    <h2 class="text-sm font-medium text-zinc-100">
                                        <?= htmlspecialchars($tarea['titulo']) ?>
                                    </h2>
                                    <span class="text-[11px] font-medium px-2 py-0.5 rounded-md border <?= clasePrioridad($prioridad) ?>">
                                        <?= htmlspecialchars($prioridad) ?>
                                    </span>
                                </div>
                                <p class="text-xs text-zinc-400 whitespace-pre-line leading-relaxed">
                                    <?= htmlspecialchars($tarea['descripcion']) ?>
                                </p>
                            </div>

                            <form action="tareas.php" method="POST"
                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta tarea?');">
                                <input type="hidden" name="eliminar_id" value="<?= (int) $tarea['id'] ?>">
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 bg-zinc-950 hover:bg-rose-950/60 text-zinc-400 hover:text-rose-400 border border-zinc-800 hover:border-rose-900/60 font-medium text-xs px-3 py-1.5 rounded-lg transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Eliminar</span>
                                </button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>

</body>

</html>
