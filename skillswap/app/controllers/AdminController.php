<?php
/**
 * AdminController — panel de administración completo.
 */
class AdminController {

    private Usuario  $usuarioModel;
    private Materia  $materiaModel;
    private Denuncia $denunciaModel;
    private Solicitud $solicitudModel;
    private BlockchainPago $blockchainModel;

    public function __construct() {
        Session::requireRol('admin');
        $this->usuarioModel    = new Usuario();
        $this->materiaModel    = new Materia();
        $this->denunciaModel   = new Denuncia();
        $this->solicitudModel  = new Solicitud();
        $this->blockchainModel = new BlockchainPago();
    }

    /** Dashboard principal. */
    public function index(): void {
        $tutoresPendientes  = $this->usuarioModel->listar('tutor', 'pendiente');
        $denunciasPendientes = $this->denunciaModel->pendientes();
        $ultimasSolicitudes = $this->solicitudModel->todas();
        $ultimosPagos       = $this->blockchainModel->todos();

        renderizar('admin/dashboard', compact(
            'tutoresPendientes', 'denunciasPendientes',
            'ultimasSolicitudes', 'ultimosPagos'
        ));
    }

    /** Lista de usuarios con filtros. */
    public function usuarios(): void {
        $rol    = $_GET['rol']    ?? '';
        $estado = $_GET['estado'] ?? '';
        $usuarios = $this->usuarioModel->listar($rol, $estado);
        renderizar('admin/usuarios', compact('usuarios', 'rol', 'estado'));
    }

    /** Aprobar tutor pendiente → activo. */
    public function aprobarTutor(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('admin');
        $id = (int)($_POST['id'] ?? 0);
        $this->usuarioModel->cambiarEstado($id, 'activo');
        Session::flash('exito', 'Tutor aprobado correctamente.');
        redirigir('admin');
    }

    /** Rechazar tutor → vuelve a alumno (o se puede eliminar). */
    public function rechazarTutor(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('admin');
        $id = (int)($_POST['id'] ?? 0);
        $this->usuarioModel->cambiarEstado($id, 'pendiente');
        // Opción: dejar en pendiente con un mensaje o degradar a alumno
        $this->usuarioModel->cambiarRol($id, 'alumno');
        Session::flash('exito', 'Solicitud de tutor rechazada. El usuario vuelve a ser alumno.');
        redirigir('admin');
    }

    /** Bloquear usuario. */
    public function bloquear(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('admin_usuarios');
        $id = (int)($_POST['id'] ?? 0);
        $this->usuarioModel->cambiarEstado($id, 'bloqueado');
        Session::flash('exito', 'Usuario bloqueado.');
        redirigir('admin_usuarios');
    }

    /** Desbloquear usuario. */
    public function desbloquear(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('admin_usuarios');
        $id = (int)($_POST['id'] ?? 0);
        $this->usuarioModel->cambiarEstado($id, 'activo');
        Session::flash('exito', 'Usuario desbloqueado.');
        redirigir('admin_usuarios');
    }

    /** Panel de denuncias. */
    public function denuncias(): void {
        $denuncias = $this->denunciaModel->todas();
        $exito     = Session::getFlash('exito');
        renderizar('admin/denuncias', compact('denuncias', 'exito'));
    }

    /** Resolver denuncia (con opción de bloquear al denunciado). */
    public function resolverDenuncia(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirigir('admin_denuncias');

        $id         = (int)($_POST['denuncia_id'] ?? 0);
        $resolucion = trim($_POST['resolucion'] ?? '');
        $accion     = $_POST['accion'] ?? '';   // 'resolver' | 'bloquear'

        if ($accion === 'bloquear') {
            $denunciadoId = (int)($_POST['denunciado_id'] ?? 0);
            $this->usuarioModel->cambiarEstado($denunciadoId, 'bloqueado');
        }

        $this->denunciaModel->resolver($id, $resolucion ?: 'Sin comentario.');
        Session::flash('exito', 'Denuncia resuelta.');
        redirigir('admin_denuncias');
    }

    /** Pagos Blockchain (solo lectura). */
    public function blockchain(): void {
        $pagos = $this->blockchainModel->todos();
        renderizar('admin/blockchain', compact('pagos'));
    }

    /** CRUD materias (alta y baja rápida). */
    public function materias(): void {
        $exito = Session::getFlash('exito');
        $error = Session::getFlash('error');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'crear') {
                $nombre = trim($_POST['nombre'] ?? '');
                $area   = trim($_POST['area']   ?? '');
                if (strlen($nombre) < 3 || empty($area)) {
                    Session::flash('error', 'Nombre y área son obligatorios.');
                } else {
                    conectar()->prepare('INSERT INTO materias (nombre, area) VALUES (?,?)')
                              ->execute([$nombre, $area]);
                    Session::flash('exito', 'Materia creada.');
                }
            }

            if ($accion === 'desactivar') {
                $mid = (int)($_POST['materia_id'] ?? 0);
                conectar()->prepare('UPDATE materias SET activa=0 WHERE id=?')->execute([$mid]);
                Session::flash('exito', 'Materia desactivada.');
            }

            redirigir('admin_materias');
        }

        $materias = conectar()->query('SELECT * FROM materias ORDER BY area, nombre')->fetchAll();
        renderizar('admin/materias', compact('materias', 'exito', 'error'));
    }
}
