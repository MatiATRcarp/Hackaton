<?php
/**
 * AuthController — login, registro (alumno/tutor), logout.
 */
class AuthController {

    private Usuario $usuarioModel;

    public function __construct() { $this->usuarioModel = new Usuario(); }

    public function inicio(): void {
        $logueado = Session::isLoggedIn();
        renderizar('auth/inicio', compact('logueado'));
    }

    public function login(): void {
        if (Session::isLoggedIn()) $this->redirigirPorRol();

        $error = '';
        $info  = match($_GET['msg'] ?? '') {
            'sesion'        => 'Necesitás iniciar sesión para continuar.',
            'registro'      => '¡Registro exitoso! Ya podés iniciar sesión.',
            'pendiente'     => 'Tu cuenta de tutor está pendiente de aprobación.',
            'logout'        => 'Cerraste sesión correctamente.',
            default         => '',
        };
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email']    ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Completá todos los campos.';
            } else {
                $usuario = $this->usuarioModel->buscarPorEmail($email);

                if ($usuario && $this->usuarioModel->verificarPassword($password, $usuario['password'])) {
                    if ($usuario['estado'] === 'bloqueado') {
                        $error = 'Tu cuenta está bloqueada. Contactá al administrador.';
                    } else {
                        Session::login($usuario);
                        $this->redirigirPorRol();
                    }
                } else {
                    $error = 'Email o contraseña incorrectos.';
                }
            }
        }

        renderizar('auth/login', compact('error', 'info', 'email'));
    }

    /** Registro como ALUMNO. */
    public function registroAlumno(): void {
        if (Session::isLoggedIn()) $this->redirigirPorRol();

        $error = '';
        $datos = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre'       => trim($_POST['nombre']       ?? ''),
                'email'        => trim($_POST['email']        ?? ''),
                'password'     => $_POST['password']  ?? '',
                'password2'    => $_POST['password2'] ?? '',
                'carrera'      => trim($_POST['carrera']      ?? ''),
                'anio_cursado' => (int)($_POST['anio_cursado'] ?? 1),
                'universidad'  => trim($_POST['universidad']  ?? ''),
            ];

            $error = $this->validarDatosBase($datos);

            if (!$error) {
                try {
                    $this->usuarioModel->crearAlumno($datos);
                    redirigir('login', ['msg' => 'registro']);
                } catch (PDOException $e) {
                    $error = 'El correo ya está registrado.';
                }
            }
        }

        renderizar('auth/registro_alumno', compact('error', 'datos'));
    }

    /** Registro como TUTOR (requiere certificado). */
    public function registroTutor(): void {
        if (Session::isLoggedIn()) $this->redirigirPorRol();

        $error = '';
        $datos = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre'       => trim($_POST['nombre']       ?? ''),
                'email'        => trim($_POST['email']        ?? ''),
                'password'     => $_POST['password']  ?? '',
                'password2'    => $_POST['password2'] ?? '',
                'carrera'      => trim($_POST['carrera']      ?? ''),
                'anio_cursado' => (int)($_POST['anio_cursado'] ?? 1),
                'universidad'  => trim($_POST['universidad']  ?? ''),
                'bio'          => trim($_POST['bio']          ?? ''),
                'wallet'       => trim($_POST['wallet']       ?? ''),
            ];

            $error = $this->validarDatosBase($datos);

            if (!$error && empty($_FILES['certificado']['name'])) {
                $error = 'Debés adjuntar un certificado de alumno regular.';
            }

            if (!$error) {
                $certPath = $this->subirCertificado();
                if (!$certPath) {
                    $error = 'Error al subir el certificado. Solo se aceptan PDF, JPG o PNG (máx. 10 MB).';
                }
            }

            if (!$error) {
                try {
                    $this->usuarioModel->crearTutor($datos, $certPath);
                    redirigir('login', ['msg' => 'pendiente']);
                } catch (PDOException) {
                    $error = 'El correo ya está registrado.';
                }
            }
        }

        renderizar('auth/registro_tutor', compact('error', 'datos'));
    }

    public function logout(): void {
        Session::destroy();
        redirigir('login', ['msg' => 'logout']);
    }

    // ── Privados ──────────────────────────────────────────────

    private function validarDatosBase(array $d): string {
        if (empty($d['nombre']) || empty($d['email']) || empty($d['password'])) {
            return 'Completá todos los campos obligatorios.';
        }
        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            return 'El correo electrónico no es válido.';
        }
        if (strlen($d['password']) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }
        if ($d['password'] !== $d['password2']) {
            return 'Las contraseñas no coinciden.';
        }
        if ($this->usuarioModel->buscarPorEmail($d['email'])) {
            return 'Ya existe una cuenta con ese correo electrónico.';
        }
        return '';
    }

    /** Sube el certificado y devuelve la ruta relativa, o false si falla. */
    private function subirCertificado(): string|false {
        $file    = $_FILES['certificado'];
        $maxSize = 10 * 1024 * 1024; // 10 MB
        $allowed = ['image/jpeg', 'image/png', 'application/pdf'];

        if ($file['error'] !== UPLOAD_ERR_OK)      return false;
        if ($file['size'] > $maxSize)               return false;
        if (!in_array($file['type'], $allowed))     return false;

        $ext    = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombre = 'cert_' . uniqid('', true) . '.' . strtolower($ext);
        $dest   = UPLOAD_PATH . '/' . $nombre;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return false;

        return 'uploads/certificados/' . $nombre;
    }

    private function redirigirPorRol(): never {
        match(Session::userRol()) {
            'admin'  => redirigir('admin'),
            'tutor'  => redirigir('tutor'),
            default  => redirigir('alumno'),
        };
    }
}
