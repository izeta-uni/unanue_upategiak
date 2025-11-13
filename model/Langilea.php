<?php

class Langilea {

    public ?int $id;
    public string $izena;
    public string $abizena;
    public string $email;
    public string $departamentua;
    public ?string $kargua;
    public ?string $kontratazio_data;
    public string $erabiltzailea;
    public string $pasahitza;
    public bool $is_admin;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->izena = $data['izena'] ?? '';
        $this->abizena = $data['abizena'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->departamentua = $data['departamentua'] ?? '';
        $this->kargua = $data['kargua'] ?? null;
        $this->kontratazio_data = $data['kontratazio_data'] ?? null;
        $this->erabiltzailea = $data['erabiltzailea'] ?? '';
        $this->pasahitza = $data['pasahitza'] ?? '';
        $this->is_admin = (bool)($data['is_admin'] ?? false);
    }

    public static function lortuLangileGuztiak(mysqli $conn): array {
        $langileak = [];
        $sql = "SELECT * FROM langileak";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result) {
            while ($langilea_data = $result->fetch_assoc()) {
                $langileak[] = new Langilea($langilea_data);
            }
        }
        $stmt->close();
        return $langileak;
    }

    public static function bilatuId(mysqli $conn, int $id): ?Langilea {
        $stmt = $conn->prepare("SELECT * FROM langileak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Langilea($result->fetch_assoc());
        }
        return null;
    }

    public static function bilatuEmail(mysqli $conn, string $email): ?Langilea {
        $stmt = $conn->prepare("SELECT * FROM langileak WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Langilea($result->fetch_assoc());
        }
        return null;
    }



    public static function bilatuErabiltzailea(mysqli $conn, string $erabiltzailea): ?Langilea {
        $stmt = $conn->prepare("SELECT * FROM langileak WHERE erabiltzailea = ?");
        $stmt->bind_param("s", $erabiltzailea);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            return new Langilea($result->fetch_assoc());
        }
        return null;
    }

    public function gorde(mysqli $conn): bool {
        if ($this->id) {
            // Eguneratu
            $stmt = $conn->prepare("UPDATE langileak SET izena = ?, abizena = ?, email = ?, departamentua = ?, kargua = ?, kontratazio_data = ?, erabiltzailea = ?, pasahitza = ?, is_admin = ? WHERE id = ?");
            $stmt->bind_param(
                "ssssssssii",
                $this->izena,
                $this->abizena,
                $this->email,
                $this->departamentua,
                $this->kargua,
                $this->kontratazio_data,
                $this->erabiltzailea,
                $this->pasahitza,
                $this->is_admin,
                $this->id
            );
        } else {
            // Sortu (Txertatu)
            $stmt = $conn->prepare("INSERT INTO langileak (izena, abizena, email, departamentua, kargua, kontratazio_data, erabiltzailea, pasahitza, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssssssssi",
                $this->izena,
                $this->abizena,
                $this->email,
                $this->departamentua,
                $this->kargua,
                $this->kontratazio_data,
                $this->erabiltzailea,
                $this->pasahitza,
                $this->is_admin
            );
        }
        
        $success = $stmt->execute();
        if ($success && !$this->id) {
            $this->id = $conn->insert_id;
        }
        $stmt->close();
        return $success;
    }

    public static function borratu(mysqli $conn, int $id): bool {
        $stmt = $conn->prepare("DELETE FROM langileak WHERE id = ?");
        $stmt->bind_param("i", $id);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
}
?>