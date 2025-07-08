<?php
require_once __DIR__ . '/../db.php';

function getSoldeEtablissement() {
    $db = getDB();
    try {
        // Utilisation de COALESCE pour gérer les valeurs NULL
        $entrees = $db->query('SELECT COALESCE(SUM(montant), 0) FROM entrant')->fetchColumn();
        $sorties = $db->query('SELECT COALESCE(SUM(montant), 0) FROM sortant')->fetchColumn();
        $prets = $db->query('SELECT COALESCE(SUM(montant), 0) FROM pret')->fetchColumn();
        
        // Conversion explicite en float pour éviter les problèmes de type
        $entrees = floatval($entrees);
        $sorties = floatval($sorties);
        $prets = floatval($prets);
        
        $solde = $entrees - $sorties - $prets;
        
        return [
            'entrees' => $entrees,
            'sorties' => $sorties,
            'prets' => $prets,
            'solde' => $solde
        ];
    } catch (PDOException $e) {
        // Log l'erreur et retourne un état par défaut
        error_log("Erreur dans getSoldeEtablissement: " . $e->getMessage());
        return [
            'entrees' => 0,
            'sorties' => 0,
            'prets' => 0,
            'solde' => 0,
            'error' => 'Erreur lors du calcul du solde'
        ];
    }
}
