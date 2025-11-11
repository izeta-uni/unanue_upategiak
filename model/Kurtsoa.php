<?php

class Kurtsoa {
    public ?int $id;
    public ?int $irakasle_id;
    public string $izena;
    public ?string $gradu_maila;
    public ?string $hizkuntza;
    public ?int $urte_kopurua;
    public ?string $sartzeko_baldintzak;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->irakasle_id = $data['irakasle_id'] ?? null;
        $this->izena = $data['izena'] ?? '';
        $this->gradu_maila = $data['gradu_maila'] ?? null;
        $this->hizkuntza = $data['hizkuntza'] ?? null;
        $this->urte_kopurua = $data['urte_kopurua'] ?? null;
        $this->sartzeko_baldintzak = $data['sartzeko_baldintzak'] ?? null;
    }

    public static function lortuGuztiak(mysqli $conn): array {
        $kurtsoak = [];
        $sql = "SELECT * FROM kurtsoak ORDER BY izena";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            foreach ($result->fetch_all(MYSQLI_ASSOC) as $kurtso_data) {
                $kurtsoak[] = new Kurtsoa($kurtso_data);
            }
        }
        return $kurtsoak;
    }

    public static function bilatuId(mysqli $conn, int $id): ?Kurtsoa {
        $stmt = $conn->prepare("SELECT * FROM kurtsoak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows === 1) {
            return new Kurtsoa($result->fetch_assoc());
        }
        $stmt->close();
        return null;
    }

    public function gorde(mysqli $conn): bool {
        if ($this->id) {
            // Eguneratu
            $stmt = $conn->prepare("UPDATE kurtsoak SET izena = ?, gradu_maila = ?, hizkuntza = ?, urte_kopurua = ?, sartzeko_baldintzak = ? WHERE id = ?");
            $stmt->bind_param("sssisi", $this->izena, $this->gradu_maila, $this->hizkuntza, $this->urte_kopurua, $this->sartzeko_baldintzak, $this->id);
        } else {
            // Sortu (Txertatu)
            $stmt = $conn->prepare("INSERT INTO kurtsoak (izena, gradu_maila, hizkuntza, urte_kopurua, sartzeko_baldintzak) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $this->izena, $this->gradu_maila, $this->hizkuntza, $this->urte_kopurua, $this->sartzeko_baldintzak);
        }
        
        $success = $stmt->execute();
        if ($success && !$this->id) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();
        return $success;
    }

    public static function borratu(mysqli $conn, int $id): bool {
        $stmt = $conn->prepare("DELETE FROM kurtsoak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>