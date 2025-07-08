<?php
require_once __DIR__ . '/../models/Client.php';

// Liste tous les clients
Flight::route('GET /client', function() {
    $clients = Client::all();
    // Ajout de valeurs vides si null pour éviter undefined côté JS
    foreach ($clients as &$c) {
        if (!isset($c['prenom'])) $c['prenom'] = '';
        if (!isset($c['date_naissance'])) $c['date_naissance'] = '';
    }
    Flight::json($clients);
});

// Récupère un client par ID
Flight::route('GET /client/@id', function($id) {
    $client = Client::find($id);
    if ($client) {
        Flight::json($client);
    } else {
        Flight::halt(404, 'Client non trouvé');
    }
});

// Ajoute un client
Flight::route('POST /client', function() {
    $nom = Flight::request()->data->nom;
    $prenom = Flight::request()->data->prenom;
    $cin = Flight::request()->data->cin;
    $date_naissance = Flight::request()->data->date_naissance;
    if (!$nom || !$cin) {
        Flight::halt(400, 'Nom et CIN obligatoires');
    }
    $id = Client::create([
        'nom' => $nom,
        'prenom' => $prenom,
        'cin' => $cin,
        'date_naissance' => $date_naissance
    ]);
    Flight::json([ 'id' => $id ]);
});

// Modifie un client
Flight::route('PUT /client/@id', function($id) {
    $nom = Flight::request()->data->nom;
    $cin = Flight::request()->data->cin;
    $client = Client::find($id);
    if (!$client) {
        Flight::halt(404, 'Client non trouvé');
    }
    Client::update($id, [ 'nom' => $nom, 'cin' => $cin ]);
    Flight::json([ 'success' => true ]);
});

// Supprime un client
Flight::route('DELETE /client/@id', function($id) {
    $client = Client::find($id);
    if (!$client) {
        Flight::halt(404, 'Client non trouvé');
    }
    Client::delete($id);
    Flight::json([ 'success' => true ]);
});
