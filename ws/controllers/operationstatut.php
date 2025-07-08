<?php
require_once __DIR__ . '/../models/OperationStatut.php';

Flight::route('GET /operationstatut', function() {
    $data = OperationStatut::all();
    Flight::json($data);
});

Flight::route('POST /operationstatut', function() {
    $data = Flight::request()->data->getData();
    OperationStatut::create($data);
    Flight::json(['message' => 'Statut d\'opération ajouté']);
});

Flight::route('DELETE /operationstatut', function() {
    $data = Flight::request()->data->getData();
    OperationStatut::delete($data['operation_id'], $data['statut_id']);
    Flight::json(['message' => 'Statut d\'opération supprimé']);
});
