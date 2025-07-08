<?php
require_once __DIR__ . '/../models/Operation.php';

// Récupérer toutes les opérations
Flight::route('GET /operation', function () {
    $data = Operation::all();
    Flight::json($data);
});

// Récupérer les opérations par prêt (ex: /operation?pret_id=1)
Flight::route('GET /operation@byPret', function () {
    $pret_id = Flight::request()->query['pret_id'];
    if (!$pret_id) {
        Flight::halt(400, json_encode(['error' => 'pret_id requis']));
    }
    $data = Operation::byPret($pret_id);
    Flight::json($data);
});

// Récupérer une opération par son ID
Flight::route('GET /operation/@id', function ($id) {
    $data = Operation::find($id);
    if (!$data) {
        Flight::halt(404, json_encode(['error' => 'Opération non trouvée']));
    }
    Flight::json($data);
});

// Créer une nouvelle opération
Flight::route('POST /operation', function () {
    $data = Flight::request()->data->getData();
    $id = Operation::create($data);
    Flight::json(['message' => 'Opération créée', 'id' => $id]);
});

// Modifier une opération existante
Flight::route('PUT /operation/@id', function ($id) {
    $data = Flight::request()->data->getData();
    $success = Operation::update($id, $data);
    Flight::json(['message' => $success ? 'Mise à jour réussie' : 'Échec de mise à jour']);
});

// Supprimer une opération
Flight::route('DELETE /operation/@id', function ($id) {
    $success = Operation::delete($id);
    Flight::json(['message' => $success ? 'Suppression réussie' : 'Échec de suppression']);
});

// Marquer une opération comme remboursée
Flight::route('POST /operation/pay', function () {
    $data = Flight::request()->data->getData();
    
    if (!isset($data['operation_id'])) {
        Flight::halt(400, json_encode(['error' => 'operation_id requis']));
    }

    $success = Operation::pay($data['operation_id']);
    
    if ($success) {
        Flight::json(['message' => 'Opération marquée comme remboursée']);
    } else {
        Flight::halt(500, json_encode(['error' => 'Échec du changement de statut']));
    }
});

// Marquer une opération comme remboursée via PUT /operation/@id/pay
Flight::route('PUT /operation/@id/pay', function ($id) {
    $success = Operation::pay($id);

    if ($success) {
        Flight::json(['message' => 'Opération marquée comme remboursée']);
    } else {
        Flight::halt(500, json_encode(['error' => 'Échec du changement de statut']));
    }
});

