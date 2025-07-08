<?php
// Inclusion des contrôleurs REST
require_once __DIR__ . '/../controllers/client.php';
require_once __DIR__ . '/../controllers/typepret.php';
require_once __DIR__ . '/../controllers/statut.php';
require_once __DIR__ . '/../controllers/pret.php';
require_once __DIR__ . '/../controllers/fonds.php';
require_once __DIR__ . '/../controllers/operation.php';
require_once __DIR__ . '/../controllers/pretstatut.php';
require_once __DIR__ . '/../controllers/operationstatut.php';
require_once __DIR__ . '/../controllers/motif.php';
require_once __DIR__ . '/../controllers/entrant.php';
require_once __DIR__ . '/../controllers/sortant.php';

// Route personnalisée : Intérêts gagnés par mois pour l'établissement financier
require_once __DIR__ . '/../models/Pret.php';

Flight::route('GET /interets', function() {
    $type_pret_id = Flight::request()->query['type_pret_id'] ?? null;
    $date_debut = Flight::request()->query['date_debut'] ?? null;
    $date_fin = Flight::request()->query['date_fin'] ?? null;
    if (!$type_pret_id || !$date_debut || !$date_fin) {
        Flight::halt(400, 'Paramètres manquants');
    }
    require_once __DIR__ . '/../models/Pret.php';
    require_once __DIR__ . '/../db.php';
    $pretModel = new Pret(getDB());
    $result = $pretModel->interetsParMois($type_pret_id, $date_debut, $date_fin);
    Flight::json($result);
});

// Route de simulation de prêt
Flight::route('POST /pret/simuler', function() {
    $data = Flight::request()->data->getData();
    $result = simulatePret($data);
    Flight::json($result);
});

Flight::route('GET /simulation-degressif', function() {
    $data = [
        'montant' => (float)$_GET['montant'],
        'taux_annuel' => (float)$_GET['taux'],
        'duree' => (int)$_GET['duree'],
        'mode' => 'degressif'
    ];
    $resultat = simulatePret($data);
    Flight::json($resultat);
});

Flight::route('GET /simulation-constant', function() {
    $data = [
        'montant' => (float)$_GET['montant'],
        'taux_annuel' => (float)$_GET['taux'],
        'duree' => (int)$_GET['duree'],
        'mode' => 'constant'
    ];
    $resultat = simulatePret($data);
    Flight::json($resultat);
});

// Route pour obtenir le tableau d'amortissement depuis la base de données
Flight::route('GET /pret/@id/amortissement', function($id) {
    require_once __DIR__ . '/../helpers/interets.php';
    $tableau = tableauAmortissementFromDB($id);
    if (!$tableau) {
        Flight::halt(404, 'Prêt non trouvé');
    }
    Flight::json($tableau);
});
