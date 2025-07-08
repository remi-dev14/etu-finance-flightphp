<?php
require_once __DIR__ . '/../db.php';

class OperationStatut {
    public static function all() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM operation_statut");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($operation_id, $statut_id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM operation_statut WHERE operation_id = ? AND statut_id = ?");
        $stmt->execute([$operation_id, $statut_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO operation_statut (operation_id, statut_id, date_modif) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['operation_id'],
            $data['statut_id'],
            $data['date_modif']
        ]);
        return true;
    }

    public static function delete($operation_id, $statut_id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM operation_statut WHERE operation_id = ? AND statut_id = ?");
        return $stmt->execute([$operation_id, $statut_id]);
    }
}
