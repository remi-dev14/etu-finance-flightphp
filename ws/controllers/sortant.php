<?php
require_once __DIR__ . '/../models/Sortant.php';
require_once __DIR__ . '/../db.php';

Flight::route('GET /sortant', function() {
    $data = Sortant::all();
    Flight::json($data);
});

Flight::route('GET /sortant/@id', function($id) {
    $data = Sortant::find($id);
    if ($data) Flight::json($data);
    else Flight::halt(404, "Sortie non trouvée");
});

// Ajouter une sortie de fonds
Flight::route('POST /sortant', function() {
    $pdo = getDB();
    $data = Flight::request()->data;

    if (empty($data->montant) || empty($data->motif_id)) {
        Flight::json(['error' => 'Montant et motif requis'], 400);
        return;
    }

    $montant = abs(floatval($data->montant)); // Assurez-vous que le montant est positif
    try {
        // Vérifier si le solde est suffisant avant la sortie
        require_once __DIR__ . '/../helpers/fonds.php';
        $solde = getSoldeEtablissement();
        if ($solde['solde'] < $montant) {
            Flight::json(['error' => 'Solde insuffisant pour cette opération'], 400);
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO sortant (montant, motif_id, date) VALUES (?, ?, NOW())");
        $stmt->execute([$montant, $data->motif_id]);
        Flight::json(['message' => 'Sortie de fonds ajoutée avec succès']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur lors de l\'ajout de la sortie de fonds'], 500);
    }
});

Flight::route('PUT /sortant/@id', function($id) {
    $data = Flight::request()->data->getData();
    Sortant::update($id, $data);
    Flight::json(['message' => 'Sortie modifiée']);
});

Flight::route('DELETE /sortant/@id', function($id) {
    Sortant::delete($id);
    Flight::json(['message' => 'Sortie supprimée']);
});
