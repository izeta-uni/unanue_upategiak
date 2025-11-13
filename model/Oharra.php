<?php

class Oharra {

    public ?int $id;
    public string $titulua;
    public string $edukia;
    public string $data; // DATETIME formatuan

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->titulua = $data['titulua'] ?? '';
        $this->edukia = $data['edukia'] ?? '';
        $this->data = $data['data'] ?? date('Y-m-d H:i:s'); // Lehenetsia uneko data eta ordua
    }

    public static function lortuOharGuztiak(mysqli $conn): array {
        $oharrak = [];
        $sql = "SELECT * FROM oharrak ORDER BY data DESC"; // Azkenak lehenik
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($oharra_data = $result->fetch_assoc()) {
                $oharrak[] = new Oharra($oharra_data);
            }
        }
        $stmt->close();
        return $oharrak;
    }

    public static function bilatuId(mysqli $conn, int $id): ?Oharra {
        $stmt = $conn->prepare("SELECT * FROM oharrak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Oharra($result->fetch_assoc());
        }
        return null;
    }

    public function gorde(mysqli $conn): bool {
        if ($this->id) {
            // Eguneratu
            $stmt = $conn->prepare("UPDATE oharrak SET titulua = ?, edukia = ?, data = ? WHERE id = ?");
            $stmt->bind_param("sssi", $this->titulua, $this->edukia, $this->data, $this->id);
        } else {
            // Sortu (Txertatu)
            $stmt = $conn->prepare("INSERT INTO oharrak (titulua, edukia, data) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $this->titulua, $this->edukia, $this->data);
        }
        
        $success = $stmt->execute();
        if ($success && !$this->id) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();
        return $success;
    }

    public static function borratu(mysqli $conn, int $id): bool {
        $stmt = $conn->prepare("DELETE FROM oharrak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
