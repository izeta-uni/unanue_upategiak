<?php

class Matrikula {
    public ?int $id;
    public int $ikasle_id;
    public int $kurtso_id;
    public ?string $matrikula_data;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->ikasle_id = $data['ikasle_id'] ?? 0;
        $this->kurtso_id = $data['kurtso_id'] ?? 0;
        $this->matrikula_data = $data['matrikula_data'] ?? null;
    }

    public static function ikasleaMatrikulatutaDago(mysqli $conn, int $ikasle_id): bool {
        $stmt = $conn->prepare("SELECT kurtso_id FROM matrikulak WHERE pertsona_id = ?");
        
        if ($stmt === false) {
            throw new Exception("SQL kontsulta prestatzean errorea ('matrikulak' taula egiaztatu): " . $conn->error);
        }
        
        $stmt->bind_param("i", $ikasle_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->num_rows > 0;
    }

    public static function lortuIkaslearenMatrikulaKurtsoId(mysqli $conn, int $ikasle_id): ?int {
        $stmt = $conn->prepare("SELECT kurtso_id FROM matrikulak WHERE pertsona_id = ?");
        $stmt->bind_param("i", $ikasle_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return (int)$row['kurtso_id'];
        }
        $stmt->close();
        return null;
    }
    
    public static function ezabatuIkaslearenMatrikula(mysqli $conn, int $ikasle_id): bool {
        $stmt = $conn->prepare("DELETE FROM matrikulak WHERE pertsona_id = ?");
        $stmt->bind_param("i", $ikasle_id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function gorde(mysqli $conn): bool {
        
        if (self::ikasleaMatrikulatutaDago($conn, $this->ikasle_id)) {
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO matrikulak (pertsona_id, kurtso_id) VALUES (?, ?)");

        if ($stmt === false) {
            throw new Exception("SQL INSERT prestatzean errorea ('matrikulak' taula egiaztatu): " . $conn->error);
        }

        $stmt->bind_param("ii", $this->ikasle_id, $this->kurtso_id);
        
        $success = $stmt->execute();
        if ($success) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();
        return $success;
    }
}
?>