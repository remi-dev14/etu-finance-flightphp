<?php
require_once __DIR__ . '/../models/Statut.php';

Flight::route('GET /statut', function() {
    $data = Statut::all();
    Flight::json($data);
});

Flight::route('GET /statut/@id', function($id) {
    $data = Statut::find($id);
    if ($data) Flight::json($data);
    else Flight::halt(404, "Statut non trouvé");
});

Flight::route('POST /statut', function() {
    $data = Flight::request()->data->getData();
    $id = Statut::create($data);
    Flight::json(['message' => 'Statut ajouté', 'id' => $id]);
});

Flight::route('PUT /statut/@id', function($id) {
    $data = Flight::request()->data->getData();
    Statut::update($id, $data);
    Flight::json(['message' => 'Statut modifié']);
});

Flight::route('DELETE /statut/@id', function($id) {
    Statut::delete($id);
    Flight::json(['message' => 'Statut supprimé']);
});
