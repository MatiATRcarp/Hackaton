<?php
/**
 * SkillSwap — Front controller / Router
 */
declare(strict_types=1);

// ── Bootstrap ────────────────────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/helpers/funciones.php';
require_once __DIR__ . '/app/helpers/Session.php';

// Modelos
foreach (glob(__DIR__ . '/app/models/*.php') as $f) require_once $f;

// Controllers
foreach (glob(__DIR__ . '/app/controllers/*.php') as $f) require_once $f;

Session::start();

// ── Routing ──────────────────────────────────────────────────
$p = $_GET['p'] ?? 'inicio';

try {
    match($p) {
        // ── Públicas ──────────────────────────────────────────
        'inicio'            => (new AuthController())->inicio(),
        'login'             => (new AuthController())->login(),
        'registro_alumno'   => (new AuthController())->registroAlumno(),
        'registro_tutor'    => (new AuthController())->registroTutor(),
        'logout'            => (new AuthController())->logout(),

        // ── Admin ─────────────────────────────────────────────
        'admin'             => (new AdminController())->index(),
        'admin_usuarios'    => (new AdminController())->usuarios(),
        'admin_aprobar'     => (new AdminController())->aprobarTutor(),
        'admin_rechazar'    => (new AdminController())->rechazarTutor(),
        'admin_bloquear'    => (new AdminController())->bloquear(),
        'admin_desbloquear' => (new AdminController())->desbloquear(),
        'admin_denuncias'   => (new AdminController())->denuncias(),
        'admin_resolver'    => (new AdminController())->resolverDenuncia(),
        'admin_blockchain'  => (new AdminController())->blockchain(),
        'admin_materias'    => (new AdminController())->materias(),

        // ── Tutor ─────────────────────────────────────────────
        'tutor'             => (new TutorController())->index(),
        'tutor_perfil'      => (new TutorController())->perfil(),
        'tutor_horarios'    => (new TutorController())->horarios(),
        'tutor_responder'   => (new TutorController())->responderSolicitud(),
        'tutor_finalizar'   => (new TutorController())->finalizarTutoria(),
        'tutor_cancelar'    => (new TutorController())->cancelar(),
        'tutor_chat'        => (new TutorController())->chat(),

        // ── Alumno ────────────────────────────────────────────
        'alumno'            => (new AlumnoController())->index(),
        'alumno_perfil'     => (new AlumnoController())->perfil(),
        'alumno_buscar'     => (new AlumnoController())->buscar(),
        'alumno_tutor'      => (new AlumnoController())->verTutor(),
        'alumno_solicitar'  => (new AlumnoController())->enviarSolicitud(),
        'alumno_cancelar'   => (new AlumnoController())->cancelar(),
        'alumno_calificar'  => (new AlumnoController())->calificar(),
        'alumno_chat'       => (new AlumnoController())->chat(),
        'alumno_denunciar'  => (new AlumnoController())->denunciar(),
        'alumno_convertir'  => (new AlumnoController())->convertirATutor(),

        default             => (new AuthController())->inicio(),
    };
} catch (Throwable $e) {
    // En MVP mostramos el error; en producción se loguearía
    http_response_code(500);
    echo '<div style="padding:2rem;font-family:sans-serif;color:#b91c1c;">
        <strong>Error interno:</strong> ' . e($e->getMessage()) . '
    </div>';
}
