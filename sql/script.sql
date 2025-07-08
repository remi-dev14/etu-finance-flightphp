-- 1) Supprime l’ancienne base si elle existe
DROP DATABASE IF EXISTS gestion_pret;

-- 2) Crée la base
CREATE DATABASE gestion_pret CHARACTER SET utf8mb4;

-- 3) Utilise la base
USE gestion_pret;

-- 4) Table client
CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    cin VARCHAR(12) UNIQUE NOT NULL,
    date_naissance DATE
);

-- 5) Table type_pret
CREATE TABLE type_pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    taux_annuel DECIMAL(5,2) NOT NULL,
    montant_min DECIMAL(12,2) NOT NULL,
    montant_max DECIMAL(12,2) NOT NULL,
    duree_min INT NOT NULL,
    duree_max INT NOT NULL
);

-- 6) Table pret (corrigée avec DATETIME)
CREATE TABLE pret (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    type_pret_id INT NOT NULL,
    montant DECIMAL(12,2) NOT NULL,
    duree INT NOT NULL,
    date_pret DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES client(id) ON DELETE CASCADE,
    FOREIGN KEY (type_pret_id) REFERENCES type_pret(id) ON DELETE CASCADE
);

-- 7) Table statut
CREATE TABLE statut (
    id INT AUTO_INCREMENT PRIMARY KEY,
    valeur VARCHAR(50) NOT NULL
);

-- 8) Table pret_statut
CREATE TABLE pret_statut (
    pret_id INT NOT NULL,
    statut_id INT NOT NULL,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pret_id) REFERENCES pret(id) ON DELETE CASCADE,
    FOREIGN KEY (statut_id) REFERENCES statut(id) ON DELETE CASCADE
);

-- 9) Table operation
CREATE TABLE operation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pret_id INT NOT NULL,
    mois INT NOT NULL,
    annee INT NOT NULL,
    emprunt_restant DECIMAL(12,2),
    interet_mensuel DECIMAL(12,2),
    montant_rembourse DECIMAL(12,2),
    montant_echeance DECIMAL(12,2), -- Montant à rembourser ce mois
    date_echeance DATE, -- Optionnel : si tu veux la date prévue de paiement
    valeur_note TEXT,
    FOREIGN KEY (pret_id) REFERENCES pret(id) ON DELETE CASCADE
);

-- 10) Table operation_statut
CREATE TABLE operation_statut (
    operation_id INT NOT NULL,
    statut_id INT NOT NULL,
    date_modif DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (operation_id) REFERENCES operation(id) ON DELETE CASCADE,
    FOREIGN KEY (statut_id) REFERENCES statut(id) ON DELETE CASCADE
);

-- 11) Table motif
CREATE TABLE motif (
    id INT AUTO_INCREMENT PRIMARY KEY,
    motif TEXT NOT NULL
);

-- 12) Table entrant (corrigée)
CREATE TABLE entrant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(12,2) NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    motif_id INT,
    FOREIGN KEY (motif_id) REFERENCES motif(id) ON DELETE SET NULL
);

-- 13) Table sortant (corrigée)
CREATE TABLE sortant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    montant DECIMAL(12,2) NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    motif_id INT,
    FOREIGN KEY (motif_id) REFERENCES motif(id) ON DELETE SET NULL
);





CREATE VIEW vue_clients_prets AS
SELECT 
    c.id AS client_id,
    CONCAT(c.nom, ' ', c.prenom) AS nom_complet,
    c.cin,
    tp.nom AS type_pret,
    p.montant,
    p.duree,
    p.date_pret
FROM pret p
JOIN client c ON p.client_id = c.id
JOIN type_pret tp ON p.type_pret_id = tp.id;
CREATE VIEW vue_pret_statut_actuel AS
SELECT 
    ps.pret_id,
    s.valeur AS statut,
    ps.date_modif
FROM pret_statut ps
JOIN (
    SELECT pret_id, MAX(date_modif) AS max_date
    FROM pret_statut
    GROUP BY pret_id
) latest ON ps.pret_id = latest.pret_id AND ps.date_modif = latest.max_date
JOIN statut s ON ps.statut_id = s.id;

CREATE VIEW vue_operation_details AS
SELECT 
    o.id AS operation_id,
    o.pret_id,
    c.nom,
    c.prenom,
    o.annee,
    o.mois,
    o.emprunt_restant,
    o.interet_mensuel,
    o.montant_rembourse,
    o.montant_echeance,
    o.date_echeance,
    o.valeur_note
FROM operation o
JOIN pret p ON o.pret_id = p.id
JOIN client c ON p.client_id = c.id;

CREATE VIEW vue_operation_statut_actuel AS
SELECT 
    os.operation_id,
    s.valeur AS statut,
    os.date_modif
FROM operation_statut os
JOIN (
    SELECT operation_id, MAX(date_modif) AS max_date
    FROM operation_statut
    GROUP BY operation_id
) latest ON os.operation_id = latest.operation_id AND os.date_modif = latest.max_date
JOIN statut s ON os.statut_id = s.id;

CREATE VIEW vue_entrees_sorties AS
SELECT 
    'entrant' AS type_mouvement,
    e.id,
    e.montant,
    e.date,
    m.motif
FROM entrant e
LEFT JOIN motif m ON e.motif_id = m.id

UNION ALL

SELECT 
    'sortant' AS type_mouvement,
    s.id,
    s.montant,
    s.date,
    m.motif
FROM sortant s
LEFT JOIN motif m ON s.motif_id = m.id;
