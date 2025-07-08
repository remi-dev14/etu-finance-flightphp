<?php
require_once __DIR__ . '/../db.php';

class PretStatut {
    public static function all() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM pret_statut");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($pret_id, $statut_id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM pret_statut WHERE pret_id = ? AND statut_id = ?");
        $stmt->execute([$pret_id, $statut_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO pret_statut (pret_id, statut_id, date_modif) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['pret_id'],
            $data['statut_id'],
            $data['date_modif']
        ]);
        return true;
    }

    public static function delete($pret_id, $statut_id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM pret_statut WHERE pret_id = ? AND statut_id = ?");
        return $stmt->execute([$pret_id, $statut_id]);
    }
}
