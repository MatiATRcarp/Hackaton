<?php
/**
 * Model: Horario
 * El tutor administra sus bloques de disponibilidad.
 */
class Horario {

    private PDO $pdo;

    public function __construct() { $this->pdo = conectar(); }

    /** Horarios futuros del tutor. */
    public function delTutor(int $tutorId, bool $soloLibres = false): array {
        $extra = $soloLibres ? 'AND estado = "libre"' : '';
        $s = $this->pdo->prepare("
            SELECT * FROM horarios
            WHERE tutor_id = ? AND fecha >= CURDATE() $extra
            ORDER BY fecha, hora_inicio
        ");
        $s->execute([$tutorId]);
        return $s->fetchAll();
    }

    /** Todos los horarios (pasados + futuros) del tutor. */
    public function todosDelTutor(int $tutorId): array {
        $s = $this->pdo->prepare('
            SELECT * FROM horarios WHERE tutor_id=? ORDER BY fecha DESC, hora_inicio DESC
        ');
        $s->execute([$tutorId]);
        return $s->fetchAll();
    }

    public function buscarPorId(int $id): ?array {
        $s = $this->pdo->prepare('SELECT * FROM horarios WHERE id=?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }

    /**
     * Crea un horario. El trigger de MySQL se encarga del control de solapamiento.
     * Devuelve el ID o lanza excepción si hay solapamiento.
     */
    public function crear(int $tutorId, string $fecha, string $horaIni, string $horaFin): int {
        // Validación básica en PHP antes de llegar al trigger
        if ($horaIni >= $horaFin) {
            throw new RuntimeException('La hora de inicio debe ser anterior a la de fin.');
        }
        // El trigger lanzará SQLSTATE 45000 si hay solapamiento
        $s = $this->pdo->prepare('
            INSERT INTO horarios (tutor_id, fecha, hora_inicio, hora_fin) VALUES (?,?,?,?)
        ');
        $s->execute([$tutorId, $fecha, $horaIni, $horaFin]);
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Elimina un horario solo si está libre (no puede borrar uno ocupado).
     */
    public function eliminar(int $id, int $tutorId): bool {
        $s = $this->pdo->prepare('
            DELETE FROM horarios WHERE id=? AND tutor_id=? AND estado="libre"
        ');
        $s->execute([$id, $tutorId]);
        return $s->rowCount() > 0;
    }

    /** Libera un horario (cuando se cancela la solicitud). */
    public function liberar(int $id): void {
        $this->pdo->prepare('UPDATE horarios SET estado="libre" WHERE id=?')->execute([$id]);
    }
}
