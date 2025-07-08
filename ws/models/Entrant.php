<?php
require_once __DIR__ . '/../db.php';

class Entrant {
    public static function all() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM entrant");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM entrant WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO entrant (montant, date, motif_id) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['montant'],
            $data['date'],
            $data['motif_id']
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE entrant SET montant = ?, date = ?, motif_id = ? WHERE id = ?");
        return $stmt->execute([
            $data['montant'],
            $data['date'],
            $data['motif_id'],
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM entrant WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
