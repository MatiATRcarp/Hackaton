<?php
/**
 * Model: Materia
 */
class Materia {

    private PDO $pdo;

    public function __construct() { $this->pdo = conectar(); }

    public function listarTodas(): array {
        return $this->pdo->query('SELECT * FROM materias WHERE activa=1 ORDER BY area, nombre')->fetchAll();
    }

    public function porArea(): array {
        $rows = $this->listarTodas();
        $agrupadas = [];
        foreach ($rows as $r) {
            $agrupadas[$r['area']][] = $r;
        }
        return $agrupadas;
    }

    /** Materias del tutor. */
    public function delTutor(int $tutorId): array {
        $s = $this->pdo->prepare('
            SELECT m.* FROM materias m
            JOIN tutor_materias tm ON tm.materia_id = m.id
            WHERE tm.tutor_id = ?
            ORDER BY m.nombre
        ');
        $s->execute([$tutorId]);
        return $s->fetchAll();
    }

    /** IDs que enseña el tutor. */
    public function idsTutor(int $tutorId): array {
        $s = $this->pdo->prepare('SELECT materia_id FROM tutor_materias WHERE tutor_id=?');
        $s->execute([$tutorId]);
        return array_column($s->fetchAll(), 'materia_id');
    }

    /** Materias del alumno (las que busca). */
    public function delAlumno(int $alumnoId): array {
        $s = $this->pdo->prepare('
            SELECT m.* FROM materias m
            JOIN alumno_materias am ON am.materia_id = m.id
            WHERE am.alumno_id = ?
            ORDER BY m.nombre
        ');
        $s->execute([$alumnoId]);
        return $s->fetchAll();
    }

    public function idsAlumno(int $alumnoId): array {
        $s = $this->pdo->prepare('SELECT materia_id FROM alumno_materias WHERE alumno_id=?');
        $s->execute([$alumnoId]);
        return array_column($s->fetchAll(), 'materia_id');
    }

    /** Reemplaza las materias del tutor (transacción). */
    public function guardarTutor(int $tutorId, array $ids): void {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM tutor_materias WHERE tutor_id=?')->execute([$tutorId]);
            $s = $this->pdo->prepare('INSERT INTO tutor_materias (tutor_id, materia_id) VALUES (?,?)');
            foreach (array_unique($ids) as $mid) {
                $s->execute([$tutorId, (int)$mid]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** Reemplaza las materias del alumno. */
    public function guardarAlumno(int $alumnoId, array $ids): void {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare('DELETE FROM alumno_materias WHERE alumno_id=?')->execute([$alumnoId]);
            $s = $this->pdo->prepare('INSERT INTO alumno_materias (alumno_id, materia_id) VALUES (?,?)');
            foreach (array_unique($ids) as $mid) {
                $s->execute([$alumnoId, (int)$mid]);
            }
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function buscarPorId(int $id): ?array {
        $s = $this->pdo->prepare('SELECT * FROM materias WHERE id=?');
        $s->execute([$id]);
        return $s->fetch() ?: null;
    }
}
