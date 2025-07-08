<?php
require_once __DIR__ . '/../models/Motif.php';

Flight::route('GET /motif', function() {
    $data = Motif::all();
    Flight::json($data);
});

Flight::route('GET /motif/@id', function($id) {
    $data = Motif::find($id);
    if ($data) Flight::json($data);
    else Flight::halt(404, "Motif non trouvé");
});

Flight::route('POST /motif', function() {
    $data = Flight::request()->data->getData();
    $id = Motif::create($data);
    Flight::json(['message' => 'Motif ajouté', 'id' => $id]);
});

Flight::route('PUT /motif/@id', function($id) {
    $data = Flight::request()->data->getData();
    Motif::update($id, $data);
    Flight::json(['message' => 'Motif modifié']);
});

Flight::route('DELETE /motif/@id', function($id) {
    Motif::delete($id);
    Flight::json(['message' => 'Motif supprimé']);
});
