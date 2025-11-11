<?php

class Pertsona {

    public ?int $id;
    public string $izena;
    public string $abizena;
    public ?string $jaiotze_data;
    public string $nan;
    public string $emaila;
    public string $rola; // 'ikasle' edo 'admin'

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->izena = $data['izena'] ?? '';
        $this->abizena = $data['abizena'] ?? '';
        $this->jaiotze_data = $data['jaiotze_data'] ?? null;
        $this->nan = $data['nan'] ?? '';
        $this->emaila = $data['emaila'] ?? '';
        $this->rola = $data['rola'] ?? 'ikasle'; // Lehenetsitako rola
    }

    public static function lortuIkasleGuztiak(mysqli $conn): array {
        $pertsonak = [];
        // 'ikasle' rola duten pertsonak bakarrik hautatu
        $sql = "SELECT * FROM pertsonak WHERE rola = 'ikasle'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            foreach ($result->fetch_all(MYSQLI_ASSOC) as $pertsona_data) {
                $pertsonak[] = new Pertsona($pertsona_data);
            }
        }
        return $pertsonak;
    }

    public static function bilatuId(mysqli $conn, int $id): ?Pertsona {
        $stmt = $conn->prepare("SELECT * FROM pertsonak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Pertsona($result->fetch_assoc());
        }
        return null;
    }

    public static function bilatuEmaila(mysqli $conn, string $emaila): ?Pertsona {
        $stmt = $conn->prepare("SELECT * FROM pertsonak WHERE emaila = ?");
        $stmt->bind_param("s", $emaila);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Pertsona($result->fetch_assoc());
        }
        return null;
    }

    public static function bilatuNan(mysqli $conn, string $nan): ?Pertsona {
        $stmt = $conn->prepare("SELECT * FROM pertsonak WHERE nan = ?");
        $stmt->bind_param("s", $nan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Pertsona($result->fetch_assoc());
        }
        return null;
    }

    public function gorde(mysqli $conn): bool {
        if ($this->id) {
            // Eguneratu
            $stmt = $conn->prepare("UPDATE pertsonak SET izena = ?, abizena = ?, jaiotze_data = ?, nan = ?, emaila = ?, rola = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $this->izena, $this->abizena, $this->jaiotze_data, $this->nan, $this->emaila, $this->rola, $this->id);
        } else {
            // Sortu (Txertatu)
            $stmt = $conn->prepare("INSERT INTO pertsonak (izena, abizena, jaiotze_data, nan, emaila, rola) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $this->izena, $this->abizena, $this->jaiotze_data, $this->nan, $this->emaila, $this->rola);
        }
        
        $success = $stmt->execute();
        if ($success && !$this->id) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();
        return $success;
    }

    public static function borratu(mysqli $conn, int $id): bool {
        $stmt = $conn->prepare("DELETE FROM pertsonak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>