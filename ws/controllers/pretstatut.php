<?php
require_once __DIR__ . '/../models/PretStatut.php';

Flight::route('GET /pretstatut', function() {
    $data = PretStatut::all();
    Flight::json($data);
});

Flight::route('POST /pretstatut', function() {
    $data = Flight::request()->data->getData();
    PretStatut::create($data);
    Flight::json(['message' => 'Statut de prêt ajouté']);
});

Flight::route('DELETE /pretstatut', function() {
    $data = Flight::request()->data->getData();
    PretStatut::delete($data['pret_id'], $data['statut_id']);
    Flight::json(['message' => 'Statut de prêt supprimé']);
});
