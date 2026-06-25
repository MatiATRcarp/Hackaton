<?php
/**
 * Model: SolicitudMateria
 * Solicitudes de alta de nueva materia enviadas por alumnos o tutores.
 *
 * SQL requerido (ejecutar una sola vez):
 * CREATE TABLE solicitudes_materia (
 *   id          INT AUTO_INCREMENT PRIMARY KEY,
 *   usuario_id  INT NOT NULL,
 *   nombre      VARCHAR(100) NOT NULL,
 *   area        VARCHAR(60)  NOT NULL,
 *   motivo      TEXT,
 *   estado      ENUM('pendiente','aprobada','rechazada') DEFAULT 'pendiente',
 *   admin_nota  TEXT,
 *   created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
 * );
 */
class SolicitudMateria {

    private PDO $pdo;

    public function __construct() { $this->pdo = conectar(); }

    public function crear(int $usuarioId, string $nombre, string $area, string $motivo): int {
        $s = $this->pdo->prepare(
            'INSERT INTO solicitudes_materia (usuario_id, nombre, area, motivo) VALUES (?,?,?,?)'
        );
        $s->execute([$usuarioId, $nombre, $area, $motivo]);
        return (int)$this->pdo->lastInsertId();
    }

    public function pendientes(): array {
        return $this->pdo->query(
            'SELECT sm.*, u.nombre AS usuario_nombre, u.rol AS usuario_rol
             FROM solicitudes_materia sm
             JOIN usuarios u ON u.id = sm.usuario_id
             WHERE sm.estado = \'pendiente\'
             ORDER BY sm.created_at ASC'
        )->fetchAll();
    }

    public function todas(): array {
        return $this->pdo->query(
            'SELECT sm.*, u.nombre AS usuario_nombre, u.rol AS usuario_rol
             FROM solicitudes_materia sm
             JOIN usuarios u ON u.id = sm.usuario_id
             ORDER BY sm.created_at DESC'
        )->fetchAll();
    }

    public function delUsuario(int $usuarioId): array {
        $s = $this->pdo->prepare(
            'SELECT * FROM solicitudes_materia WHERE usuario_id = ? ORDER BY created_at DESC'
        );
        $s->execute([$usuarioId]);
        return $s->fetchAll();
    }

    /** Aprobar: crea la materia y marca la solicitud como aprobada. */
    public function aprobar(int $id, string $nota = ''): void {
        $s = $this->pdo->prepare('SELECT * FROM solicitudes_materia WHERE id=?');
        $s->execute([$id]);
        $sol = $s->fetch();
        if (!$sol || $sol['estado'] !== 'pendiente') return;

        $this->pdo->beginTransaction();
        try {
            // Crear la materia en el catálogo
            $this->pdo->prepare('INSERT INTO materias (nombre, area) VALUES (?,?)')
                      ->execute([$sol['nombre'], $sol['area']]);

            // Marcar solicitud como aprobada
            $this->pdo->prepare(
                'UPDATE solicitudes_materia SET estado=\'aprobada\', admin_nota=? WHERE id=?'
            )->execute([$nota ?: 'Materia aprobada y agregada al catálogo.', $id]);

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function rechazar(int $id, string $nota): void {
        $this->pdo->prepare(
            'UPDATE solicitudes_materia SET estado=\'rechazada\', admin_nota=? WHERE id=?'
        )->execute([$nota ?: 'Solicitud rechazada.', $id]);
    }

    public function yaEnvioPendiente(int $usuarioId, string $nombre): bool {
        $s = $this->pdo->prepare(
            'SELECT id FROM solicitudes_materia
             WHERE usuario_id=? AND LOWER(nombre)=LOWER(?) AND estado=\'pendiente\''
        );
        $s->execute([$usuarioId, $nombre]);
        return (bool)$s->fetch();
    }
}
