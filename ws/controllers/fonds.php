<?php
require_once __DIR__ . '/../db.php';

Flight::route('GET /fonds', function() {
    $db = getDB();

    // Total des entrées
    $entrant = $db->query("SELECT SUM(montant) as total FROM entrant")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total des sorties
    $sortant = $db->query("SELECT SUM(montant) as total FROM sortant")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total des prêts
    $prets = $db->query("SELECT SUM(montant) as total FROM pret")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total des remboursements
    $remb = $db->query("SELECT SUM(montant_rembourse) as total FROM operation")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Solde final disponible
    $solde = floatval($entrant) - floatval($sortant) - floatval($prets) + floatval($remb);

    Flight::json([
        'solde' => $solde,
        'entrees' => floatval($entrant),
        'sorties' => floatval($sortant),
        'prets' => floatval($prets),
        'remboursements' => floatval($remb)
    ]);
});

// Route pour obtenir le solde total actuel
Flight::route('GET /fonds/total', function() {
    $db = getDB();
    $entrant = $db->query("SELECT SUM(montant) as total FROM entrant")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $sortant = $db->query("SELECT SUM(montant) as total FROM sortant")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    $solde = floatval($entrant) - floatval($sortant);
    Flight::json(['solde' => $solde]);
});

// Route avancée S4 : tableau du montant total à disposition de l'EF par mois
Flight::route('GET /fonds/disponible', function() {
    $db = getDB();
    $debut = Flight::request()->query['debut'] ?? date('Y-m-01');
    $fin = Flight::request()->query['fin'] ?? date('Y-m-t');

    $start = new DateTime($debut);
    $end = new DateTime($fin);
    $end->modify('first day of next month');
    $interval = new DateInterval('P1M');
    $period = new DatePeriod($start, $interval, $end);

    $result = [];
    foreach ($period as $dt) {
        $mois = $dt->format('Y-m');
        $entrees = $db->query("SELECT COALESCE(SUM(montant),0) FROM entrant WHERE date <= '{$mois}-31'")->fetchColumn();
        $sorties = $db->query("SELECT COALESCE(SUM(montant),0) FROM sortant WHERE date <= '{$mois}-31'")->fetchColumn();
        $prets = $db->query("SELECT COALESCE(SUM(montant),0) FROM pret WHERE date_pret <= '{$mois}-31'")->fetchColumn();
        $remb = $db->query("SELECT COALESCE(SUM(montant_rembourse),0) FROM operation WHERE echeance <= '{$mois}-31'")->fetchColumn();
        $disponible = floatval($entrees) - floatval($sorties) - floatval($prets) + floatval($remb);
        $result[] = [
            'mois' => $mois,
            'entrees' => floatval($entrees),
            'sorties' => floatval($sorties),
            'prets' => floatval($prets),
            'remboursements' => floatval($remb),
            'disponible' => $disponible
        ];
    }
    Flight::json($result);
});
