<?php
require_once __DIR__ . '/../models/TypePret.php';

Flight::route('GET /typepret', function() {
    $data = TypePret::all();
    Flight::json($data);
});

Flight::route('GET /typepret/@id', function($id) {
    $data = TypePret::find($id);
    if ($data) Flight::json($data);
    else Flight::halt(404, "Type de prêt non trouvé");
});

Flight::route('POST /typepret', function() {
    $data = Flight::request()->data->getData();
    $id = TypePret::create($data);
    Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id]);
});

Flight::route('PUT /typepret/@id', function($id) {
    $data = Flight::request()->data->getData();
    TypePret::update($id, $data);
    Flight::json(['message' => 'Type de prêt modifié']);
});

Flight::route('DELETE /typepret/@id', function($id) {
    TypePret::delete($id);
    Flight::json(['message' => 'Type de prêt supprimé']);
});
