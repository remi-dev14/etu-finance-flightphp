<?php
require_once __DIR__ . '/../models/Pret.php';

$pretModel = new Pret(getDB());

Flight::route('GET /pret', function() use ($pretModel) {
    $data = $pretModel->all();
    Flight::json($data);
});

// (Optionnel) à implémenter si tu veux la recherche par ID
// Flight::route('GET /pret/@id', function($id) use ($pretModel) {
//     $data = $pretModel->find($id);
//     if ($data) Flight::json($data);
//     else Flight::halt(404, "Prêt non trouvé");
// });

Flight::route('POST /pret', function() use ($pretModel) {
    $data = Flight::request()->data->getData();
    $client_id = $data['client_id'] ?? null;
    $type_pret_id = $data['type_pret_id'] ?? null;
    $montant = $data['montant'] ?? null;
    $duree = $data['duree'] ?? null;
    $assurance = $data['assurance'] ?? 0;
    $delai = $data['delai'] ?? 0;

    if (!$client_id || !$type_pret_id || !$montant || !$duree) {
        Flight::halt(400, 'Paramètres manquants');
    }
    // Appelle create() avec tous les paramètres
    $pret_id = $pretModel->create($client_id, $type_pret_id, $montant, $duree, $assurance, $delai);
    Flight::json(['message' => 'Prêt ajouté', 'id' => $pret_id]);
});


// (Optionnel) à implémenter si tu veux la modification/suppression
// Flight::route('PUT /pret/@id', function($id) use ($pretModel) {
//     $data = Flight::request()->data->getData();
//     $pretModel->update($id, $data);
//     Flight::json(['message' => 'Prêt modifié']);
// });

Flight::route('DELETE /pret/@id', function($id) use ($pretModel) {
    $pretModel->delete($id);
    Flight::json(['message' => 'Prêt supprimé']);
});
