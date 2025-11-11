<?php

class Kontua {
    public ?int $id;
    public ?int $pertsona_id;
    public string $erabiltzaile_izena;
    private string $pasahitza;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->pertsona_id = $data['pertsona_id'] ?? null;
        $this->erabiltzaile_izena = $data['erabiltzaile_izena'] ?? '';
        $this->pasahitza = $data['pasahitza'] ?? '';
    }

    public function setPasahitza(string $pasahitza_gordina): void {
        $this->pasahitza = password_hash($pasahitza_gordina, PASSWORD_DEFAULT);
    }

    public function egiaztatuPasahitza(string $pasahitza_gordina): bool {
        return password_verify($pasahitza_gordina, $this->pasahitza);
    }

    public static function bilatuErabiltzaileIzenez(mysqli $conn, string $erabiltzaile_izena): ?Kontua {
        $stmt = $conn->prepare("SELECT * FROM kontuak WHERE erabiltzaile_izena = ?");
        $stmt->bind_param("s", $erabiltzaile_izena);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Kontua($result->fetch_assoc());
        }
        return null;
    }

    public static function bilatuPertsonaId(mysqli $conn, int $pertsona_id): ?Kontua {
        $stmt = $conn->prepare("SELECT * FROM kontuak WHERE pertsona_id = ?");
        $stmt->bind_param("i", $pertsona_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Kontua($result->fetch_assoc());
        }
        return null;
    }

    public function gorde(mysqli $conn): bool {
        if ($this->id) {
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO kontuak (pertsona_id, erabiltzaile_izena, pasahitza) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $this->pertsona_id, $this->erabiltzaile_izena, $this->pasahitza);
        
        $success = $stmt->execute();
        if ($success) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();
        return $success;
    }
}
?>