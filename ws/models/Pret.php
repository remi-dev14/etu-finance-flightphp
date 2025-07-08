<?php
require_once __DIR__ . '/../db.php';

class Pret {
    private $db;
    public function __construct($db) { $this->db = $db; }


    public function all() {
        $stmt = $this->db->query("
            SELECT p.*, c.nom AS client_nom, t.nom AS type_nom
            FROM pret p
            JOIN client c ON p.client_id = c.id
            JOIN type_pret t ON p.type_pret_id = t.id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function create($client_id, $type_pret_id, $montant, $duree, $assurance = 0, $delai = 0) {
    // 1) Récupérer taux annuel
    $stmt = $this->db->prepare("SELECT taux_annuel FROM type_pret WHERE id = ?");
    $stmt->execute([$type_pret_id]);
    $type = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$type) throw new Exception("Type de prêt inconnu");

    $taux_annuel = $type['taux_annuel'];

    // 2) Insérer le prêt
    $stmt = $this->db->prepare("INSERT INTO pret (client_id, type_pret_id, montant, duree, date_pret) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$client_id, $type_pret_id, $montant, $duree]);
    $pret_id = $this->db->lastInsertId();

    // 3) Simuler le prêt (obtenir le plan mensuel)
    require_once __DIR__ . '/../helpers/interets_helper.php';
    $simulation = simulatePret([
        'montant' => $montant,
        'taux_annuel' => $taux_annuel,
        'duree' => $duree,
        'assurance' => $assurance,
        'delai' => $delai,
        'parAnnee' => false // plan mensuel
    ]);
    $plan = $simulation['plan'];

    // 4) Date de départ pour les échéances (aujourd’hui ou date_pret)
    $mois_base = date('m');
    $annee_base = date('Y');

    // 5) Insérer opérations (échéances)
    $stmt = $this->db->prepare("INSERT INTO operation (pret_id, mois, annee, emprunt_restant, interet_mensuel, montant_rembourse, montant_echeance, date_echeance, valeur_note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($plan as $index => $ligne) {
        $mois = ($mois_base + $index) % 12;
        $annee = $annee_base + floor(($mois_base + $index) / 12);
        if ($mois === 0) $mois = 12;
        $date_echeance = sprintf('%04d-%02d-01', $annee, $mois);

        $stmt->execute([
            $pret_id,
            $mois,
            $annee,
            $ligne['emprunt_restant_du'],
            $ligne['interet'],
            0.00,                  // montant remboursé au départ = 0
            $ligne['annuite'],
            $date_echeance,
            'Prévu'
        ]);
    }

    return $pret_id;
}



    public function interetsParMois($type_pret_id, $date_debut, $date_fin) {
        $stmt = $this->db->prepare("
            SELECT 
                DATE_FORMAT(date_pret, '%Y-%m') AS mois,
                SUM(montant * (taux_annuel / 100) / 12) AS interets
            FROM pret
            JOIN type_pret ON pret.type_pret_id = type_pret.id
            WHERE pret.type_pret_id = ?
              AND date_pret BETWEEN ? AND ?
            GROUP BY mois
            ORDER BY mois ASC
        ");
        $stmt->execute([$type_pret_id, $date_debut, $date_fin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pret WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
