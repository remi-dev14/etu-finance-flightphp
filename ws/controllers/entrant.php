<?php
require_once __DIR__ . '/../models/Entrant.php';
require_once __DIR__ . '/../db.php';

Flight::route('GET /entrant', function() {
    $data = Entrant::all();
    Flight::json($data);
});

Flight::route('GET /entrant/@id', function($id) {
    $data = Entrant::find($id);
    if ($data) Flight::json($data);
    else Flight::halt(404, "Entrée non trouvée");
});

// Ajouter une entrée de fonds
Flight::route('POST /entrant', function() {
    $pdo = getDB();
    $data = Flight::request()->data;

    if (empty($data->montant) || empty($data->motif_id)) {
        Flight::json(['error' => 'Montant et motif requis'], 400);
        return;
    }

    $montant = floatval($data->montant);
    if ($montant <= 0) {
        Flight::json(['error' => 'Le montant doit être positif'], 400);
        return;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO entrant (montant, motif_id, date) VALUES (?, ?, NOW())");
        $stmt->execute([$montant, $data->motif_id]);
        Flight::json(['message' => 'Entrée de fonds ajoutée avec succès']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Erreur lors de l\'ajout des fonds'], 500);
    }
});

Flight::route('PUT /entrant/@id', function($id) {
    $data = Flight::request()->data->getData();
    Entrant::update($id, $data);
    Flight::json(['message' => 'Entrée modifiée']);
});

Flight::route('DELETE /entrant/@id', function($id) {
    Entrant::delete($id);
    Flight::json(['message' => 'Entrée supprimée']);
});
