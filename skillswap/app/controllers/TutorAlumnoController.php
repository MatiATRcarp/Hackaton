<?php
/**
 * TutorController — dashboard del tutor.
 */
class TutorController {

    private Usuario   $usuarioModel;
    private Materia   $materiaModel;
    private Horario   $horarioModel;
    private Solicitud $solicitudModel;
    private ChatMensaje $chatModel;

    public function __construct() {
        Session::requireRol('tutor');
        if (Session::get('usuario_estado') === 'pendiente') {
            renderizar('auth/pendiente');
            exit;
        }
        $this->usuarioModel   = new Usuario();
        $this->materiaModel   = new Materia();
        $this->horarioModel   = new Horario();
        $this->solicitudModel = new Solicitud();
        $this->chatModel      = new ChatMensaje();
    }

    public function index(): void {
        $uid        = Session::userId();
        $solicitudes = $this->solicitudModel->recibidasPorTutor($uid);
        $horarios    = $this->horarioModel->delTutor($uid);
        $materias    = $this->materiaModel->delTutor($uid);
        $exito       = Session::getFlash('exito');
        $error       = Session::getFlash('error');

        renderizar('tutor/dashboard', compact('solicitudes', 'horarios', 'materias', 'exito', 'error'));
    }

    /** Perfil del tutor: datos + materias. */
    public function perfil(): void {
        $uid     = Session::userId();
        $usuario = $this->usuarioModel->buscarPorId($uid);
        $todasMaterias = $this->materiaModel->porArea();
        $misIds  = $this->materiaModel->idsTutor($uid);
        $exito   = Session::getFlash('exito');
        $error   = Session::getFlash('error');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'perfil') {
                $datos = [
                    'nombre'       => trim($_POST['nombre']      ?? ''),
                    'carrera'      => trim($_POST['carrera']     ?? ''),
                    'anio_cursado' => (int)($_POST['anio']       ?? 1),
                    'universidad'  => trim($_POST['universidad'] ?? ''),
                    'bio'          => trim($_POST['bio']         ?? ''),
                ];
                if (empty($datos['nombre'])) {
                    Session::flash('error', 'El nombre no puede estar vacío.');
                } else {
                    $this->usuarioModel->actualizarPerfil($uid, $datos);
                    Session::set('usuario_nombre', $datos['nombre']);
                    Session::flash('exito', 'Perfil actualizado.');
                }
                redirigir('tutor_perfil');
            }

            if ($accion === 'materias') {
                $ids = array_map('intval', $_POST['materias'] ?? []);
                try {
                    $this->materiaModel->guardarTutor($uid, $ids);
                    Session::flash('exito', 'Materias actualizadas.');
                } catch (Throwable) {
                    Session::flash('error', 'Error al guardar materias.');
                }
                redirigir('tutor_perfil');
            }
        }

        renderizar('tutor/perfil', compact('usuario', 'todasMaterias', 'misIds', 'exito', 'error'));
    }

    /** Gestión de horarios. */
    public function horarios(): void {
        $uid     = Session::userId();
        $horarios = $this->horarioModel->todosDelTutor($uid);
        $exito    = Session::getFlash('exito');
        $error    = Session::getFlash('error');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'crear') {
                $fecha = $_POST['fecha']      ?? '';
                $ini   = $_POST['hora_inicio'] ?? '';
                $fin   = $_POST['hora_fin']    ?? '';
                try {
                    $this->horarioModel->crear($uid, $fecha, $ini, $fin);
                    Session::flash('exito', 'Horario agregado.');
                } catch (Throwable $e) {
                    Session::flash('error', $e->getMessage());
                }
            }

            if ($accion === 'eliminar') {
                $hid = (int)($_POST['horario_id'] ?? 0);
                if (!$this->horarioModel->eliminar($hid, $uid)) {
                    Session::flash('error', 'No podés eliminar un horario reservado.');
                } else {
                    Session::flash('exito', 'Horario eliminado.');
                }
            }

            redirigir('tutor_horarios');
        }

        renderizar('tutor/horarios', compact('horarios', 'exito', 'error'));
    }

    /** Responder una solicitud (aceptar/rechazar). */
    public function responderSolicitud(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('tutor');
        $uid  = Session::userId();
        $sid  = (int)($_POST['solicitud_id'] ?? 0);
        $acc  = $_POST['accion'] ?? '';

        if ($this->solicitudModel->responder($sid, $uid, $acc)) {
            Session::flash('exito', 'Solicitud ' . $acc . '.');
        } else {
            Session::flash('error', 'No se pudo procesar la solicitud.');
        }
        redirigir('tutor');
    }

    /** Finalizar tutoría → genera pago Blockchain. */
    public function finalizarTutoria(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('tutor');
        $uid = Session::userId();
        $sid = (int)($_POST['solicitud_id'] ?? 0);
        try {
            if ($this->solicitudModel->finalizar($sid, $uid)) {
                Session::flash('exito', '✅ Tutoría finalizada. Se registró el pago en Blockchain.');
            }
        } catch (Throwable $e) {
            Session::flash('error', $e->getMessage());
        }
        redirigir('tutor');
    }

    /** Cancelar tutoría. */
    public function cancelar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('tutor');
        $uid = Session::userId();
        $sid = (int)($_POST['solicitud_id'] ?? 0);
        try {
            $this->solicitudModel->cancelar($sid, $uid, 'tutor');
            Session::flash('exito', 'Tutoría cancelada. Se notificó al alumno.');
        } catch (RuntimeException $e) {
            Session::flash('error', $e->getMessage());
        }
        redirigir('tutor');
    }

    /** Chat de una solicitud aceptada. */
    public function chat(): void {
        $uid  = Session::userId();
        $sid  = (int)($_GET['id'] ?? 0);
        $sol  = (new Solicitud())->buscarPorId($sid);

        if (!$sol || $sol['tutor_id'] !== $uid || $sol['estado'] !== 'aceptada') {
            redirigir('tutor');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $texto = trim($_POST['mensaje'] ?? '');
            $this->chatModel->enviar($sid, $uid, $texto);
            redirigir('tutor_chat', ['id' => $sid]);
        }

        $mensajes = $this->chatModel->deSolicitud($sid);
        $otro     = $this->usuarioModel->buscarPorId((int)$sol['alumno_id']);
        renderizar('chat/sala', compact('sol', 'mensajes', 'otro', 'uid'));
    }
}

// ══════════════════════════════════════════════════════════════

/**
 * AlumnoController — dashboard del alumno.
 */
class AlumnoController {

    private Usuario   $usuarioModel;
    private Materia   $materiaModel;
    private Horario   $horarioModel;
    private Solicitud $solicitudModel;
    private ChatMensaje $chatModel;
    private Denuncia  $denunciaModel;

    public function __construct() {
        Session::requireRol('alumno');
        $this->usuarioModel   = new Usuario();
        $this->materiaModel   = new Materia();
        $this->horarioModel   = new Horario();
        $this->solicitudModel = new Solicitud();
        $this->chatModel      = new ChatMensaje();
        $this->denunciaModel  = new Denuncia();
    }

    /** Dashboard: mis solicitudes. */
    public function index(): void {
        $uid        = Session::userId();
        $solicitudes = $this->solicitudModel->enviadasPorAlumno($uid);
        $exito       = Session::getFlash('exito');
        $error       = Session::getFlash('error');
        renderizar('alumno/dashboard', compact('solicitudes', 'exito', 'error'));
    }

    /** Perfil del alumno: datos + materias que busca. */
    public function perfil(): void {
        $uid     = Session::userId();
        $usuario = $this->usuarioModel->buscarPorId($uid);
        $todasMaterias = $this->materiaModel->porArea();
        $misIds  = $this->materiaModel->idsAlumno($uid);
        $exito   = Session::getFlash('exito');
        $error   = Session::getFlash('error');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'perfil') {
                $datos = [
                    'nombre'       => trim($_POST['nombre']      ?? ''),
                    'carrera'      => trim($_POST['carrera']     ?? ''),
                    'anio_cursado' => (int)($_POST['anio']       ?? 1),
                    'universidad'  => trim($_POST['universidad'] ?? ''),
                    'bio'          => trim($_POST['bio']         ?? ''),
                ];
                if (empty($datos['nombre'])) {
                    Session::flash('error', 'El nombre no puede estar vacío.');
                } else {
                    $this->usuarioModel->actualizarPerfil($uid, $datos);
                    Session::set('usuario_nombre', $datos['nombre']);
                    Session::flash('exito', 'Perfil actualizado.');
                }
                redirigir('alumno_perfil');
            }

            if ($accion === 'materias') {
                $ids = array_map('intval', $_POST['materias'] ?? []);
                $this->materiaModel->guardarAlumno($uid, $ids);
                Session::flash('exito', 'Materias actualizadas.');
                redirigir('alumno_perfil');
            }
        }

        renderizar('alumno/perfil', compact('usuario', 'todasMaterias', 'misIds', 'exito', 'error'));
    }

    /** Buscar tutores con filtro por materia. */
    public function buscar(): void {
        $todasMaterias = $this->materiaModel->listarTodas();
        $filtroMateria = (int)($_GET['materia'] ?? 0);

        if ($filtroMateria > 0) {
            $tutores = $this->usuarioModel->tutoresPorMateria($filtroMateria);
        } else {
            $tutores = $this->usuarioModel->tutoresActivos();
        }

        // Adjuntar materias a cada tutor
        foreach ($tutores as &$t) {
            $t['materias'] = $this->materiaModel->delTutor((int)$t['id']);
        }
        unset($t);

        renderizar('alumno/buscar', compact('tutores', 'todasMaterias', 'filtroMateria'));
    }

    /** Ver perfil público de un tutor + horarios disponibles. */
    public function verTutor(): void {
        $uid       = Session::userId();
        $tutorId   = (int)($_GET['id'] ?? 0);
        $tutor     = $this->usuarioModel->buscarPorId($tutorId);

        if (!$tutor || $tutor['rol'] !== 'tutor' || $tutor['estado'] !== 'activo') {
            redirigir('alumno_buscar');
        }

        $materias = $this->materiaModel->delTutor($tutorId);
        $horarios = $this->horarioModel->delTutor($tutorId, true); // solo libres
        renderizar('alumno/ver_tutor', compact('tutor', 'materias', 'horarios', 'uid'));
    }

    /** Enviar solicitud de tutoría. */
    public function enviarSolicitud(): void {
        $uid       = Session::userId();
        $tutorId   = (int)($_GET['id'] ?? 0);
        $tutor     = $this->usuarioModel->buscarPorId($tutorId);

        if (!$tutor || $tutor['rol'] !== 'tutor' || $tutor['estado'] !== 'activo') {
            redirigir('alumno_buscar');
        }

        $todasMaterias = $this->materiaModel->delTutor($tutorId);
        $horarios      = $this->horarioModel->delTutor($tutorId, true);
        $error         = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $horarioId  = (int)($_POST['horario_id']  ?? 0);
            $materiaId  = (int)($_POST['materia_id']  ?? 0);
            $mensaje    = trim($_POST['mensaje'] ?? '');

            if (!$horarioId || !$materiaId || strlen($mensaje) < 10) {
                $error = 'Completá todos los campos (mensaje mínimo 10 caracteres).';
            } else {
                try {
                    $this->solicitudModel->crear($uid, $tutorId, $horarioId, $materiaId, $mensaje);
                    Session::flash('exito', 'Solicitud enviada. Esperá la confirmación del tutor.');
                    redirigir('alumno');
                } catch (Throwable $e) {
                    $error = 'Error al enviar la solicitud.';
                }
            }
        }

        renderizar('alumno/enviar_solicitud', compact('tutor', 'todasMaterias', 'horarios', 'error'));
    }

    /** Cancelar solicitud. */
    public function cancelar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('alumno');
        $uid = Session::userId();
        $sid = (int)($_POST['solicitud_id'] ?? 0);
        try {
            $this->solicitudModel->cancelar($sid, $uid, 'alumno');
            Session::flash('exito', 'Tutoría cancelada. Se liberó el horario del tutor.');
        } catch (RuntimeException $e) {
            Session::flash('error', $e->getMessage());
        }
        redirigir('alumno');
    }

    /** Calificar una tutoría finalizada. */
    public function calificar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('alumno');
        $uid   = Session::userId();
        $sid   = (int)($_POST['solicitud_id'] ?? 0);
        $stars = (int)($_POST['calificacion'] ?? 0);
        $com   = trim($_POST['comentario'] ?? '');
        if ($this->solicitudModel->calificar($sid, $uid, $stars, $com)) {
            Session::flash('exito', '¡Gracias por tu calificación!');
        } else {
            Session::flash('error', 'No se pudo guardar la calificación.');
        }
        redirigir('alumno');
    }

    /** Chat con el tutor (solicitud aceptada). */
    public function chat(): void {
        $uid  = Session::userId();
        $sid  = (int)($_GET['id'] ?? 0);
        $sol  = (new Solicitud())->buscarPorId($sid);

        if (!$sol || $sol['alumno_id'] !== $uid || $sol['estado'] !== 'aceptada') {
            redirigir('alumno');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $texto = trim($_POST['mensaje'] ?? '');
            $this->chatModel->enviar($sid, $uid, $texto);
            redirigir('alumno_chat', ['id' => $sid]);
        }

        $mensajes = $this->chatModel->deSolicitud($sid);
        $otro     = $this->usuarioModel->buscarPorId((int)$sol['tutor_id']);
        renderizar('chat/sala', compact('sol', 'mensajes', 'otro', 'uid'));
    }

    /** Denunciar a un tutor. */
    public function denunciar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('alumno');
        $uid     = Session::userId();
        $tutorId = (int)($_POST['tutor_id'] ?? 0);
        $motivo  = trim($_POST['motivo']    ?? '');
        if ($tutorId && strlen($motivo) >= 10) {
            $this->denunciaModel->crear($uid, $tutorId, $motivo);
            Session::flash('exito', 'Denuncia enviada. El administrador la revisará pronto.');
        } else {
            Session::flash('error', 'El motivo debe tener al menos 10 caracteres.');
        }
        redirigir('alumno');
    }

    /** Convertirse en tutor (solicitud de conversión). */
    public function convertirATutor(): void {
        $uid   = Session::userId();
        $error = '';
        $datos = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'carrera'      => trim($_POST['carrera']     ?? ''),
                'anio_cursado' => (int)($_POST['anio']       ?? 1),
                'universidad'  => trim($_POST['universidad'] ?? ''),
                'bio'          => trim($_POST['bio']         ?? ''),
                'wallet'       => trim($_POST['wallet']      ?? ''),
            ];

            if (empty($_FILES['certificado']['name'])) {
                $error = 'Debés adjuntar un certificado de alumno regular.';
            }

            if (!$error) {
                $certPath = $this->subirCertificado();
                if (!$certPath) {
                    $error = 'Error al subir el certificado. Solo PDF, JPG o PNG (máx. 2 MB).';
                }
            }

            if (!$error) {
                $this->usuarioModel->solicitarConversionTutor($uid, $certPath, $datos);
                Session::login(array_merge(
                    $this->usuarioModel->buscarPorId($uid),
                ));
                Session::flash('exito', 'Solicitud enviada. El admin la revisará pronto.');
                redirigir('login', ['msg' => 'pendiente']);
            }
        }

        renderizar('alumno/convertir_tutor', compact('error', 'datos'));
    }

    private function subirCertificado(): string|false {
        $file    = $_FILES['certificado'] ?? null;
        if (!$file) return false;
        $maxSize = 2 * 1024 * 1024;
        $allowed = ['image/jpeg', 'image/png', 'application/pdf'];
        if ($file['error'] !== UPLOAD_ERR_OK)  return false;
        if ($file['size'] > $maxSize)           return false;
        if (!in_array($file['type'], $allowed)) return false;
        $ext    = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombre = 'cert_' . uniqid('', true) . '.' . strtolower($ext);
        $dest   = UPLOAD_PATH . '/' . $nombre;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return false;
        return 'uploads/certificados/' . $nombre;
    }
}
