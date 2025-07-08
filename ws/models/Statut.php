<?php
require_once __DIR__ . '/../db.php';

class Statut {
    public static function all() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM statut");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM statut WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO statut (valeur) VALUES (?)");
        $stmt->execute([$data['valeur']]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE statut SET valeur = ? WHERE id = ?");
        return $stmt->execute([$data['valeur'], $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM statut WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
