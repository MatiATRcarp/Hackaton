<?php
/**
 * Model: Usuario
 */
class Usuario {

    private PDO $pdo;

    public function __construct() { $this->pdo = conectar(); }

    public function buscarPorId(int $id): ?array {
        $s = $this->pdo->prepare('SELECT * FROM usuarios WHERE id = ?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    public function buscarPorEmail(string $email): ?array {
        $s = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = ?');
        $s->execute([$email]);
        return $s->fetch() ?: null;
    }

    public function verificarPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    /** Crea alumno. Devuelve ID. */
    public function crearAlumno(array $d): int {
        $s = $this->pdo->prepare('
            INSERT INTO usuarios (nombre, email, password, rol, estado, carrera, anio_cursado, universidad)
            VALUES (?, ?, ?, "alumno", "activo", ?, ?, ?)
        ');
        $s->execute([
            $d['nombre'], $d['email'],
            password_hash($d['password'], PASSWORD_DEFAULT),
            $d['carrera'], (int)$d['anio_cursado'], $d['universidad'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Crea tutor con estado 'pendiente' (necesita aprobación admin).
     * Devuelve ID.
     */
    public function crearTutor(array $d, string $certPath): int {
        $s = $this->pdo->prepare('
            INSERT INTO usuarios
                (nombre, email, password, rol, estado, carrera, anio_cursado, universidad,
                 certificado_path, wallet_address, bio)
            VALUES (?, ?, ?, "tutor", "pendiente", ?, ?, ?, ?, ?, ?)
        ');
        $s->execute([
            $d['nombre'], $d['email'],
            password_hash($d['password'], PASSWORD_DEFAULT),
            $d['carrera'], (int)$d['anio_cursado'], $d['universidad'],
            $certPath,
            $d['wallet'] ?? null,
            $d['bio']    ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function actualizarPerfil(int $id, array $d): void {
        $this->pdo->prepare('
            UPDATE usuarios SET nombre=?, carrera=?, anio_cursado=?, universidad=?, bio=?
            WHERE id=?
        ')->execute([$d['nombre'], $d['carrera'], $d['anio_cursado'], $d['universidad'], $d['bio'], $id]);
    }

    public function cambiarEstado(int $id, string $estado): void {
        $this->pdo->prepare('UPDATE usuarios SET estado=? WHERE id=?')
                  ->execute([$estado, $id]);
    }

    public function cambiarRol(int $id, string $rol): void {
        $this->pdo->prepare('UPDATE usuarios SET rol=? WHERE id=?')
                  ->execute([$rol, $id]);
    }

    /** Lista todos excepto admin. Acepta filtro de rol y estado. */
    public function listar(string $rol = '', string $estado = ''): array {
        $where = ['rol != "admin"'];
        $params = [];
        if ($rol)    { $where[] = 'rol = ?';    $params[] = $rol; }
        if ($estado) { $where[] = 'estado = ?'; $params[] = $estado; }
        $sql = 'SELECT id, nombre, email, rol, estado, carrera, anio_cursado, universidad,
                       certificado_path, wallet_address, created_at
                FROM usuarios WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC';
        $s = $this->pdo->prepare($sql);
        $s->execute($params);
        return $s->fetchAll();
    }

    /** Tutores activos (para buscar). */
    public function tutoresActivos(): array {
        $s = $this->pdo->prepare('
            SELECT id, nombre, carrera, anio_cursado, universidad, bio, wallet_address
            FROM usuarios WHERE rol="tutor" AND estado="activo" ORDER BY nombre
        ');
        $s->execute();
        return $s->fetchAll();
    }

    /** Tutores activos filtrados por materia. */
    public function tutoresPorMateria(int $materiaId): array {
        $s = $this->pdo->prepare('
            SELECT DISTINCT u.id, u.nombre, u.carrera, u.anio_cursado, u.universidad, u.bio
            FROM usuarios u
            JOIN tutor_materias tm ON tm.tutor_id = u.id
            WHERE u.rol="tutor" AND u.estado="activo" AND tm.materia_id=?
            ORDER BY u.nombre
        ');
        $s->execute([$materiaId]);
        return $s->fetchAll();
    }

    // ── Conversión alumno → tutor ─────────────────────────────
    public function solicitarConversionTutor(int $id, string $certPath, array $d): void {
        $this->pdo->prepare('
            UPDATE usuarios SET rol="tutor", estado="pendiente",
                certificado_path=?, wallet_address=?, bio=?
            WHERE id=?
        ')->execute([$certPath, $d['wallet'] ?? null, $d['bio'] ?? null, $id]);
    }
}
