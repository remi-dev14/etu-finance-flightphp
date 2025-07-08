<?php
require_once __DIR__ . '/../db.php';

class Operation {
    // Marquer une opération comme payée
    public static function pay($id) {
        $db = getDB();
        // Récupérer l'id du statut "Remboursé" ou "Remboursé totalement"
        $stmt = $db->prepare("SELECT id FROM statut WHERE valeur LIKE 'Remboursé%'");
        $stmt->execute();
        $statut = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$statut) return false;
        $statut_id = $statut['id'];
        // Insérer dans operation_statut
        $stmt2 = $db->prepare("INSERT INTO operation_statut (operation_id, statut_id, date_modif) VALUES (?, ?, NOW())");
        return $stmt2->execute([$id, $statut_id]);
    }
    public static function all() {
        $db = getDB();
        $stmt = $db->query("SELECT o.*, s.valeur as statut FROM operation o LEFT JOIN operation_statut os ON o.id = os.operation_id LEFT JOIN statut s ON os.statut_id = s.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM operation WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO operation (pret_id, mois, annee, emprunt_restant, interet_mensuel, montant_rembourse, echeance, valeur_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['pret_id'],
            $data['mois'],
            $data['annee'],
            $data['emprunt_restant'],
            $data['interet_mensuel'],
            $data['montant_rembourse'],
            $data['echeance'],
            $data['valeur_note']
        ]);
        $operation_id = $db->lastInsertId();
        // Ajout du statut initial si fourni
        if (isset($data['statut_id'])) {
            $stmt2 = $db->prepare("INSERT INTO operation_statut (operation_id, statut_id, date_modif) VALUES (?, ?, NOW())");
            $stmt2->execute([$operation_id, $data['statut_id']]);
        }
        return $operation_id;
    }
    // Récupérer les opérations d'un prêt
    public static function byPret($pret_id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT o.*, s.valeur as statut FROM operation o LEFT JOIN operation_statut os ON o.id = os.operation_id LEFT JOIN statut s ON os.statut_id = s.id WHERE o.pret_id = ?");
        $stmt->execute([$pret_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE operation SET pret_id = ?, mois = ?, annee = ?, emprunt_restant = ?, interet_mensuel = ?, montant_rembourse = ?, echeance = ?, valeur_note = ? WHERE id = ?");
        return $stmt->execute([
            $data['pret_id'],
            $data['mois'],
            $data['annee'],
            $data['emprunt_restant'],
            $data['interet_mensuel'],
            $data['montant_rembourse'],
            $data['echeance'],
            $data['valeur_note'],
            $id
        ]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM operation WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
