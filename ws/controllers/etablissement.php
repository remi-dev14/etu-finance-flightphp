<?php
require_once __DIR__ . '/../db.php';

// Liste des établissements
Flight::route('GET /etablissement', function() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM etablissement");
    $etabs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    Flight::json($etabs);
});

// Ajouter un établissement
Flight::route('POST /etablissement', function() {
    $pdo = getDB();
    $data = Flight::request()->data;

    if (empty($data->nom)) {
        Flight::json(['error' => 'Nom requis'], 400);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO etablissement (nom, fonds) VALUES (?, ?)");
    $stmt->execute([$data->nom, $data->fonds ?? 0]);

    Flight::json(['message' => 'Établissement ajouté avec succès']);
});

// Ajouter des fonds à un établissement existant
Flight::route('POST /etablissement/fonds', function() {
    $pdo = getDB();
    $data = Flight::request()->data;

    if (empty($data->etab_id) || empty($data->montant)) {
        Flight::json(['error' => 'etab_id et montant requis'], 400);
        return;
    }

    // Vérifier l’existence de l’établissement
    $stmt = $pdo->prepare("SELECT fonds FROM etablissement WHERE id = ?");
    $stmt->execute([$data->etab_id]);
    $etab = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$etab) {
        Flight::json(['error' => 'Établissement introuvable'], 404);
        return;
    }

    // Mise à jour du fonds
    $nouveauFonds = $etab['fonds'] + floatval($data->montant);
    $update = $pdo->prepare("UPDATE etablissement SET fonds = ? WHERE id = ?");
    $update->execute([$nouveauFonds, $data->etab_id]);

    Flight::json([
        'message' => 'Fonds ajoutés avec succès',
        'nouveau_fonds' => $nouveauFonds
    ]);
});
