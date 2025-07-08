<?php
// Helper pour calculs financiers (annuité constante, simulation, etc.)

/**
 * Calcule l'annuité constante d'un prêt
 * @param float $montant
 * @param float $taux_annuel (en %)
 * @param int $duree (en mois)
 * @param float $assurance (en %, optionnel)
 * @param int $delai (en mois, optionnel)
 * @return float
 */
function calculerAnnuiteConstante($montant, $taux_annuel, $duree, $assurance = 0, $delai = 0) {
    $taux_mensuel = $taux_annuel / 12 / 100;
    if ($taux_mensuel == 0) {
        $annuite = $montant / $duree;
    } else {
        $annuite = $montant * $taux_mensuel / (1 - pow(1 + $taux_mensuel, -$duree));
    }
    $assurance_mensuelle = $montant * ($assurance / 100);
    // Si délai, les premiers mois sont à 0 remboursement
    $plan = [];
    for ($i = 1; $i <= $duree; $i++) {
        $remboursement = ($i <= $delai) ? 0 : $annuite + $assurance_mensuelle;
        $plan[] = [
            'mois' => $i,
            'montant' => round($remboursement, 2)
        ];
    }
    // Calcul du capital restant dû et intérêts totaux
    $capital_restant = $montant;
    $interets_total = 0;
    $cout_total = 0;
    $plan_detaille = [];
    for ($i = 1; $i <= $duree; $i++) {
        if ($i <= $delai) {
            $interet_mensuel = $capital_restant * $taux_mensuel;
            $remboursement = 0;
        } else {
            $interet_mensuel = $capital_restant * $taux_mensuel;
            $remboursement = $annuite + $assurance_mensuelle;
            $capital_restant -= ($annuite - $interet_mensuel);
        }
        $interets_total += $interet_mensuel;
        $cout_total += $remboursement;
        $plan_detaille[] = [
            'mois' => $i,
            'montant' => round($remboursement, 2),
            'interet' => round($interet_mensuel, 2),
            'capital_restant' => round(max($capital_restant, 0), 2)
        ];
    }
    return [
        'annuite' => round($annuite, 2),
        'assurance_mensuelle' => round($assurance_mensuelle, 2),
        'plan' => $plan_detaille,
        'interets_total' => round($interets_total, 2),
        'cout_total' => round($cout_total, 2)
    ];
}




/**
 * Génère un tableau d'amortissement mensuel détaillé
 * Retourne un tableau avec les colonnes :
 * MOIS, EMPRUNT RESTANT DU, INTERET, AMORTISSEMENT, ANNUITE, VALEUR NETTE
 */
function tableauAmortissementMensuel($plan_mensuel, $montant) {
    $result = [];
    $capital_debut = $montant;
    foreach ($plan_mensuel as $ligne) {
        $interet = $ligne['interet'];
        $amortissement = $ligne['montant'] - $interet;
        $annuite = $ligne['montant'];
        $capital_fin = $ligne['capital_restant'];
        $result[] = [
            'mois' => $ligne['mois'],
            'emprunt_restant_du' => (float)$capital_debut,
            'interet' => (float)$interet,
            'amortissement' => (float)$amortissement,
            'annuite' => (float)$annuite,
            'valeur_nette' => (float)$capital_fin
        ];
        $capital_debut = $capital_fin;
    }
    return $result;
}


/**
 * Génère un tableau d'amortissement annuel détaillé
 * Retourne un tableau avec les colonnes :
 * ANNEE, EMPRUNT RESTANT DU, INTERET, AMORTISSEMENT, ANNUITE, VALEUR NETTE
 */
function tableauAmortissementAnnuel($plan_mensuel, $montant, $annuite, $assurance_mensuelle) {
    $result = [];
    $nb_mois = count($plan_mensuel);
    $capital_debut = $montant;
    $mois_par_an = 12;
    $nb_annees = ceil($nb_mois / $mois_par_an);
    for ($an = 1; $an <= $nb_annees; $an++) {
        $mois_debut = ($an - 1) * $mois_par_an;
        $mois_fin = min($an * $mois_par_an, $nb_mois);
        $interet_annee = 0;
        $amortissement_annee = 0;
        $annuite_annee = 0;
        for ($i = $mois_debut; $i < $mois_fin; $i++) {
            $interet_annee += $plan_mensuel[$i]['interet'];
            // Amortissement = remboursement - interet (sans soustraction de l'assurance)
            $amortissement = $plan_mensuel[$i]['montant'] - $plan_mensuel[$i]['interet'];
            $amortissement_annee += $amortissement;
            $annuite_annee += $plan_mensuel[$i]['montant'];
        }
        $capital_fin = $plan_mensuel[$mois_fin - 1]['capital_restant'];
        $result[] = [
            'annee' => $an,
            'emprunt_restant_du' => (float)$capital_debut,
            'interet' => (float)$interet_annee,
            'amortissement' => (float)$amortissement_annee,
            'annuite' => (float)$annuite_annee,
            'valeur_nette' => (float)$capital_fin
        ];
        $capital_debut = $capital_fin;
    }
    return $result;
}

/**
 * Simulation complète d'un prêt (plan d'amortissement annuel ou mensuel)
 * $data = [montant, taux_annuel, duree, assurance, delai, parAnnee]
 */
function simulatePret($data) {
    $montant = $data['montant'];
    $taux_annuel = $data['taux_annuel'];
    $duree = $data['duree'];
    $assurance = isset($data['assurance']) ? $data['assurance'] : 0;
    $delai = isset($data['delai']) ? $data['delai'] : 0;
    $parAnnee = isset($data['parAnnee']) ? (bool)$data['parAnnee'] : true;
    $mode = isset($data['mode']) ? $data['mode'] : 'constant'; // Ajout ici

    // ➕ MODE : Dégressif = amortissement constant
    if ($mode === 'degressif') {
        $plan = tableauAmortissementAnnuelDegressif($montant, $taux_annuel, ceil($duree / 12));
        return [
            'type' => 'annuel_degressif',
            'plan' => $plan
        ];
    }

    // Sinon : mode normal = annuité constante
    $res = calculerAnnuiteConstante($montant, $taux_annuel, $duree, $assurance, $delai);
    if ($parAnnee) {
        $tableau_annuel = tableauAmortissementAnnuel($res['plan'], $montant, $res['annuite'], $res['assurance_mensuelle']);
        return [
            'type' => 'annuel',
            'annuite' => $res['annuite'],
            'assurance_mensuelle' => $res['assurance_mensuelle'],
            'plan' => $tableau_annuel,
            'interets_total' => $res['interets_total'],
            'cout_total' => $res['cout_total']
        ];
    } else {
        $tableau_mensuel = tableauAmortissementMensuel($res['plan'], $montant);
        return [
            'type' => 'mensuel',
            'annuite' => $res['annuite'],
            'assurance_mensuelle' => $res['assurance_mensuelle'],
            'plan' => $tableau_mensuel,
            'interets_total' => $res['interets_total'],
            'cout_total' => $res['cout_total']
        ];
    }
}


function tableauAmortissementAnnuelDegressif($montant, $taux_annuel, $duree_annees) {
    $result = [];
    $amortissement = $montant / $duree_annees;
    $capital_debut = $montant;

    for ($annee = 1; $annee <= $duree_annees; $annee++) {
        $interet = $capital_debut * ($taux_annuel / 100);
        $annuite = $amortissement + $interet;
        $capital_fin = $capital_debut - $amortissement;

        $result[] = [
            'annee' => $annee,
            'emprunt_restant_du' => round($capital_debut, 2),
            'interet' => round($interet, 2),
            'amortissement' => round($amortissement, 2),
            'annuite' => round($annuite, 2),
            'valeur_nette' => round(max($capital_fin, 0), 2)
        ];

        $capital_debut = $capital_fin;
    }

    return $result;
}
function tableauAmortissementMensuelDegressif($montant, $taux_annuel, $duree_mois) {
    $result = [];
    $amortissement = $montant / $duree_mois;
    $capital_debut = $montant;

    for ($mois = 1; $mois <= $duree_mois; $mois++) {
        $interet = $capital_debut * ($taux_annuel / 100 / 12);
        $annuite = $amortissement + $interet;
        $capital_fin = $capital_debut - $amortissement;

        $result[] = [
            'mois' => $mois,
            'emprunt_restant_du' => round($capital_debut, 2),
            'interet' => round($interet, 2),
            'amortissement' => round($amortissement, 2),
            'annuite' => round($annuite, 2),
            'valeur_nette' => round(max($capital_fin, 0), 2)
        ];

        $capital_debut = $capital_fin;
    }

    return $result;
}

/**
 * Génère un tableau d'amortissement à partir des données de la base
 * @param int $pret_id ID du prêt
 * @return array Tableau d'amortissement
 */
function tableauAmortissementFromDB($pret_id) {
    require_once __DIR__ . '/../db.php';
    $db = getDB();
    
    // Récupérer les informations du prêt
    $stmt = $db->prepare("
        SELECT p.montant, p.duree, p.date_pret, tp.taux_annuel
        FROM pret p 
        JOIN type_pret tp ON p.type_pret_id = tp.id
        WHERE p.id = ?
    ");
    $stmt->execute([$pret_id]);
    $pret = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pret) {
        return null;
    }

    $montant = $pret['montant'];
    $duree = $pret['duree'];
    $taux_annuel = $pret['taux_annuel'];
    $taux_mensuel = $taux_annuel / 12 / 100;
    
    // Calculer l'amortissement mensuel constant
    $amortissement = $montant / $duree;
    $result = [];
    $capital_restant = $montant;
    
    for ($mois = 1; $mois <= $duree; $mois++) {
        $interet = $capital_restant * $taux_mensuel;
        $mensualite = $amortissement + $interet;
        
        $result[] = [
            'mois' => $mois,
            'capital_restant' => round($capital_restant, 2),
            'interet' => round($interet, 2),
            'amortissement' => round($amortissement, 2),
            'mensualite' => round($mensualite, 2)
        ];
        
        $capital_restant -= $amortissement;
    }
    
    return [
        'infos_pret' => $pret,
        'tableau' => $result,
        'total_interets' => array_sum(array_column($result, 'interet')),
        'total_mensualites' => array_sum(array_column($result, 'mensualite'))
    ];
}