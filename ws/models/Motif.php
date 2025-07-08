<?php
require_once __DIR__ . '/../db.php';

class Motif {
    public static function all() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM motif");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM motif WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO motif (motif) VALUES (?)");
        $stmt->execute([$data['motif']]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE motif SET motif = ? WHERE id = ?");
        return $stmt->execute([$data['motif'], $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM motif WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
