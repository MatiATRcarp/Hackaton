<?php
/**
 * Model: ChatMensaje
 * Solo texto, solo entre partes de una solicitud aceptada.
 */
class ChatMensaje {

    private PDO $pdo;

    public function __construct() { $this->pdo = conectar(); }

    public function deSolicitud(int $solicitudId): array {
        $s = $this->pdo->prepare('
            SELECT cm.*, u.nombre AS autor_nombre
            FROM chat_mensajes cm
            JOIN usuarios u ON u.id = cm.usuario_id
            WHERE cm.solicitud_id = ?
            ORDER BY cm.created_at ASC
        ');
        $s->execute([$solicitudId]);
        return $s->fetchAll();
    }

    /** Envía mensaje. Solo texto plano, sin imágenes. */
    public function enviar(int $solicitudId, int $usuarioId, string $texto): bool {
        $texto = trim(strip_tags($texto)); // sanitizar: solo texto
        if ($texto === '') return false;
        $this->pdo->prepare('
            INSERT INTO chat_mensajes (solicitud_id, usuario_id, mensaje) VALUES (?,?,?)
        ')->execute([$solicitudId, $usuarioId, $texto]);
        return true;
    }
}

// ─────────────────────────────────────────────────────────────

/**
 * Model: Denuncia
 */
class Denuncia {

    private PDO $pdo;

    public function __construct() { $this->pdo = conectar(); }

    public function crear(int $denuncianteId, int $denunciadoId, string $motivo): int {
        $s = $this->pdo->prepare('
            INSERT INTO denuncias (denunciante_id, denunciado_id, motivo) VALUES (?,?,?)
        ');
        $s->execute([$denuncianteId, $denunciadoId, trim($motivo)]);
        return (int)$this->pdo->lastInsertId();
    }

    public function pendientes(): array {
        $s = $this->pdo->prepare('
            SELECT d.*,
                   a.nombre AS denunciante_nombre,
                   b.nombre AS denunciado_nombre, b.email AS denunciado_email, b.estado AS denunciado_estado
            FROM denuncias d
            JOIN usuarios a ON a.id = d.denunciante_id
            JOIN usuarios b ON b.id = d.denunciado_id
            WHERE d.estado = "pendiente"
            ORDER BY d.created_at ASC
        ');
        $s->execute();
        return $s->fetchAll();
    }

    public function todas(): array {
        $s = $this->pdo->prepare('
            SELECT d.*,
                   a.nombre AS denunciante_nombre,
                   b.nombre AS denunciado_nombre
            FROM denuncias d
            JOIN usuarios a ON a.id = d.denunciante_id
            JOIN usuarios b ON b.id = d.denunciado_id
            ORDER BY d.created_at DESC
        ');
        $s->execute();
        return $s->fetchAll();
    }

    public function resolver(int $id, string $resolucion): void {
        $this->pdo->prepare('
            UPDATE denuncias SET estado="resuelta", resolucion=? WHERE id=?
        ')->execute([trim($resolucion), $id]);
    }
}

// ─────────────────────────────────────────────────────────────

/**
 * Model: BlockchainPago (simulado Sepolia Testnet)
 *
 * DECISIÓN ARQUITECTÓNICA MVP:
 * En producción real, se usaría una API como Infura/Alchemy + web3.php
 * para firmar y enviar una transacción real a Sepolia o una red L2.
 * Para el MVP se genera un tx_hash determinista (SHA256 de datos únicos)
 * que simula el comportamiento de una transacción real y se registra
 * en la tabla blockchain_pagos. El hash es verificable como prueba de
 * concepto del flujo, y la arquitectura está preparada para intercambiar
 * la función simulada por una llamada real a RPC sin cambiar el resto del código.
 */
class BlockchainPago {

    private PDO $pdo;
    private const MONTO_ETH  = 0.01;   // recompensa fija por tutoría (configurable)
    private const RED        = 'Sepolia Testnet (simulado)';

    public function __construct() { $this->pdo = conectar(); }

    /**
     * Registra el pago simulado al finalizar una tutoría.
     * Devuelve el tx_hash generado.
     */
    public function registrar(int $solicitudId, int $tutorId, int $alumnoId): string {
        $tutor = (new Usuario())->buscarPorId($tutorId);
        $wallet = $tutor['wallet_address'] ?? '0x' . bin2hex(random_bytes(20));

        // Hash simulado: SHA-256 de datos únicos (como un tx_hash real de 32 bytes = 64 hex chars)
        $txHash = '0x' . hash('sha256',
            "skillswap:sol={$solicitudId}:tutor={$tutorId}:alumno={$alumnoId}:"
            . microtime(true) . ':' . random_bytes(8)
        );

        $s = $this->pdo->prepare('
            INSERT INTO blockchain_pagos
                (solicitud_id, tutor_id, alumno_id, wallet_destino, monto_eth, tx_hash, red)
            VALUES (?,?,?,?,?,?,?)
        ');
        $s->execute([
            $solicitudId, $tutorId, $alumnoId,
            $wallet, self::MONTO_ETH, $txHash, self::RED,
        ]);

        return $txHash;
    }

    public function deSolicitud(int $solicitudId): ?array {
        $s = $this->pdo->prepare('SELECT * FROM blockchain_pagos WHERE solicitud_id=?');
        $s->execute([$solicitudId]);
        return $s->fetch() ?: null;
    }

    public function todos(): array {
        $s = $this->pdo->prepare('
            SELECT bp.*, t.nombre AS tutor_nombre, a.nombre AS alumno_nombre
            FROM blockchain_pagos bp
            JOIN usuarios t ON t.id = bp.tutor_id
            JOIN usuarios a ON a.id = bp.alumno_id
            ORDER BY bp.created_at DESC
        ');
        $s->execute();
        return $s->fetchAll();
    }
}
