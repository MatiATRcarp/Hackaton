<?php
/**
 * Model: Solicitud de tutoría
 */
class Solicitud {

    private PDO $pdo;

    public function __construct() { $this->pdo = conectar(); }

    public function buscarPorId(int $id): ?array {
        $s = $this->pdo->prepare('SELECT * FROM solicitudes WHERE id=?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    /** Crea una solicitud. El horario queda 'libre' hasta que el tutor acepte. */
    public function crear(int $alumnoId, int $tutorId, int $horarioId, int $materiaId, string $mensaje): int {
        $s = $this->pdo->prepare('
            INSERT INTO solicitudes (alumno_id, tutor_id, horario_id, materia_id, mensaje)
            VALUES (?,?,?,?,?)
        ');
        $s->execute([$alumnoId, $tutorId, $horarioId, $materiaId, $mensaje]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Acepta o rechaza (solo el tutor, solo si está pendiente).
     * El trigger SQL actualiza el estado del horario.
     */
    public function responder(int $id, int $tutorId, string $estado): bool {
        if (!in_array($estado, ['aceptada', 'rechazada'])) return false;
        $s = $this->pdo->prepare('
            UPDATE solicitudes SET estado=?
            WHERE id=? AND tutor_id=? AND estado="pendiente"
        ');
        $s->execute([$estado, $id, $tutorId]);
        return $s->rowCount() > 0;
    }

    /**
     * Cancela la solicitud.
     * Regla: solo si falta más de 1 día (24h) para el horario reservado.
     * Libera el horario si estaba aceptada.
     */
    public function cancelar(int $id, int $usuarioId, string $quien): bool {
        $sol = $this->buscarPorId($id);
        if (!$sol) return false;
        if (!in_array($sol['estado'], ['pendiente', 'aceptada'])) return false;

        // Verificar que el usuario es parte
        if ($sol['alumno_id'] !== $usuarioId && $sol['tutor_id'] !== $usuarioId) return false;

        // Verificar 24 h de anticipación
        $horario = (new Horario())->buscarPorId((int)$sol['horario_id']);
        if (!$horario) return false;
        $fechaHora = $horario['fecha'] . ' ' . $horario['hora_inicio'];
        if (strtotime($fechaHora) - time() < 86400) {
            throw new RuntimeException('Solo podés cancelar con al menos 24 horas de anticipación.');
        }

        $this->pdo->beginTransaction();
        try {
            $s = $this->pdo->prepare('
                UPDATE solicitudes SET estado="cancelada", cancelada_por=? WHERE id=?
            ');
            $s->execute([$quien, $id]);

            // El trigger maneja la liberación del horario si estaba aceptada
            // pero si estaba pendiente hay que liberar manual también (no hay trigger para eso)
            if ($sol['estado'] === 'pendiente') {
                (new Horario())->liberar((int)$sol['horario_id']);
            }

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        return true;
    }

    /**
     * El tutor marca la tutoría como finalizada y se genera el pago Blockchain.
     */
    public function finalizar(int $id, int $tutorId): bool {
        $sol = $this->buscarPorId($id);
        if (!$sol || $sol['tutor_id'] !== $tutorId || $sol['estado'] !== 'aceptada') return false;

        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('UPDATE solicitudes SET estado="finalizada" WHERE id=?')
                      ->execute([$id]);

            // Generar pago Blockchain simulado
            (new BlockchainPago())->registrar(
                (int)$id, $tutorId, (int)$sol['alumno_id']
            );

            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
        return true;
    }

    /** El alumno califica la tutoría finalizada. */
    public function calificar(int $id, int $alumnoId, int $stars, string $comentario): bool {
        $sol = $this->buscarPorId($id);
        if (!$sol || $sol['alumno_id'] !== $alumnoId || $sol['estado'] !== 'finalizada') return false;
        if ($sol['calificacion'] !== null) return false; // ya calificó

        $s = $this->pdo->prepare('
            UPDATE solicitudes SET calificacion=?, comentario_cal=? WHERE id=?
        ');
        $s->execute([max(1, min(5, $stars)), trim($comentario), $id]);
        return true;
    }

    /** Solicitudes recibidas por el tutor. */
    public function recibidasPorTutor(int $tutorId): array {
        $s = $this->pdo->prepare('
            SELECT s.*,
                   u.nombre AS alumno_nombre, u.email AS alumno_email, u.carrera AS alumno_carrera,
                   m.nombre AS materia_nombre,
                   h.fecha, h.hora_inicio, h.hora_fin
            FROM solicitudes s
            JOIN usuarios u ON u.id = s.alumno_id
            JOIN materias m ON m.id = s.materia_id
            JOIN horarios h ON h.id = s.horario_id
            WHERE s.tutor_id = ?
            ORDER BY s.created_at DESC
        ');
        $s->execute([$tutorId]);
        return $s->fetchAll();
    }

    /** Solicitudes enviadas por el alumno. */
    public function enviadasPorAlumno(int $alumnoId): array {
        $s = $this->pdo->prepare('
            SELECT s.*,
                   u.nombre AS tutor_nombre, u.email AS tutor_email, u.carrera AS tutor_carrera,
                   u.wallet_address,
                   m.nombre AS materia_nombre,
                   h.fecha, h.hora_inicio, h.hora_fin
            FROM solicitudes s
            JOIN usuarios u ON u.id = s.tutor_id
            JOIN materias m ON m.id = s.materia_id
            JOIN horarios h ON h.id = s.horario_id
            WHERE s.alumno_id = ?
            ORDER BY s.created_at DESC
        ');
        $s->execute([$alumnoId]);
        return $s->fetchAll();
    }

    /** Todas las solicitudes (panel admin). */
    public function todas(): array {
        $s = $this->pdo->prepare('
            SELECT s.*,
                   a.nombre AS alumno_nombre, t.nombre AS tutor_nombre,
                   m.nombre AS materia_nombre,
                   h.fecha, h.hora_inicio
            FROM solicitudes s
            JOIN usuarios a ON a.id = s.alumno_id
            JOIN usuarios t ON t.id = s.tutor_id
            JOIN materias m ON m.id = s.materia_id
            JOIN horarios h ON h.id = s.horario_id
            ORDER BY s.created_at DESC
        ');
        $s->execute();
        return $s->fetchAll();
    }

    /** ¿El usuario es parte de esta solicitud? */
    public function esParte(int $id, int $uid): bool {
        $s = $this->buscarPorId($id);
        if (!$s) return false;
        return $s['alumno_id'] === $uid || $s['tutor_id'] === $uid;
    }
}
