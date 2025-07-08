-- 0. Connexion et encodage
SET NAMES utf8mb4;

-- 1. Clients
INSERT INTO client (nom, prenom, cin, date_naissance) VALUES
('RAKOTO', 'Tojo', 'CIN100001', '1990-01-12'),
('RABE', 'Mialy', 'CIN100002', '1992-03-25'),
('RANDRIAM', 'Tahina', 'CIN100003', '1985-07-14'),
('RASOLO', 'Hery', 'CIN100004', '1980-11-30'),
('RAJAO', 'Lova', 'CIN100005', '1995-06-22'),
('RAKOTONIAINA', 'Tina', 'CIN100006', '1998-04-11'),
('ANDRIAM', 'Feno', 'CIN100007', '1991-09-09'),
('RAZAFY', 'Soa', 'CIN100008', '1989-02-19'),
('RAKOTOBE', 'Zo', 'CIN100009', '1993-12-01'),
('RAMIANDRISOA', 'Sarah', 'CIN100010', '1996-05-05');

-- 2. Types de prets (sans accents)
INSERT INTO type_pret (nom, taux_annuel, montant_min, montant_max, duree_min, duree_max) VALUES
('Credit Immobilier', 6.50, 10000000.00, 200000000.00, 60, 300),
('Credit Consommation', 8.90, 100000.00, 10000000.00, 6, 60),
('Credit Etudiant', 4.20, 500000.00, 5000000.00, 12, 72),
('Credit Vehicule', 7.20, 2000000.00, 60000000.00, 12, 84),
('Credit Equipement', 9.50, 1000000.00, 20000000.00, 6, 48);

-- 3. Statuts (sans accents)
INSERT INTO statut (valeur) VALUES
('En attente'),
('Valide'),
('Rejete'),
('En cours'),
('Rembourse partiellement'),
('Rembourse totalement'),
('Retard de paiement');

-- 4. Motifs (sans accents)
INSERT INTO motif (motif) VALUES
('Projet immobilier'),
('Frais de scolarite'),
('Achat de vehicule'),
('Achat de materiel'),
('Frais medicaux'),
('Paiement fournisseur'),
('Travaux maison'),
('Voyage professionnel');

-- 5. Prets
INSERT INTO pret (client_id, type_pret_id, montant, duree, date_pret) VALUES
(1, 1, 50000000.00, 240, '2023-05-01'),
(1, 2, 2000000.00, 24, '2024-02-01'),
(2, 3, 1500000.00, 36, '2023-09-15'),
(3, 4, 12000000.00, 60, '2024-01-20'),
(4, 5, 3000000.00, 18, '2024-06-01'),
(5, 2, 800000.00, 12, '2023-10-10'),
(6, 3, 2000000.00, 48, '2023-12-01'),
(7, 4, 25000000.00, 84, '2024-03-05'),
(8, 5, 5000000.00, 36, '2023-08-20'),
(9, 2, 1000000.00, 24, '2024-04-01'),
(10, 3, 1200000.00, 36, '2024-02-10');

-- 6. Statuts des prets
INSERT INTO pret_statut (pret_id, statut_id, date_modif) VALUES
(1, 2, '2023-05-03 08:30:00'),
(2, 2, '2024-02-02 09:00:00'),
(3, 2, '2023-09-17 10:00:00'),
(4, 1, '2024-01-21 09:15:00'),
(5, 2, '2024-06-03 11:00:00'),
(6, 2, '2023-10-12 12:00:00'),
(7, 2, '2023-12-03 13:00:00'),
(8, 2, '2024-03-07 14:00:00'),
(9, 2, '2023-08-22 15:00:00'),
(10, 2, '2024-04-02 16:00:00');

-- 7. Operations (notes sans accents ni caracteres speciaux)
INSERT INTO operation (
    pret_id, mois, annee, emprunt_restant, interet_mensuel, montant_rembourse, montant_echeance, date_echeance, valeur_note
) VALUES
(1, 6, 2023, 49000000.00, 270000.00, 1500000.00, 1770000.00, '2023-06-01', 'Paiement recu'),
(1, 7, 2023, 47500000.00, 265000.00, 1500000.00, 1765000.00, '2023-07-01', 'Paiement recu'),
(2, 3, 2024, 1800000.00, 140000.00, 250000.00, 390000.00, '2024-03-01', 'Retard 2 jours'),
(3, 10, 2023, 1000000.00, 50000.00, 500000.00, 550000.00, '2023-10-15', 'OK'),
(4, 6, 2024, 11500000.00, 72000.00, 600000.00, 672000.00, '2024-07-01', 'Paiement partiel');

-- 8. Statuts des operations
INSERT INTO operation_statut (operation_id, statut_id, date_modif) VALUES
(1, 4, '2023-06-01 10:00:00'),
(2, 4, '2023-07-01 10:00:00'),
(3, 7, '2024-03-03 10:00:00'),
(4, 4, '2023-10-15 10:00:00'),
(5, 5, '2024-07-01 10:00:00');

-- 9. Entrants (revenus)
INSERT INTO entrant (montant, date, motif_id) VALUES
(3000000.00, '2024-06-01', 1),
(1500000.00, '2024-06-10', 2),
(2000000.00, '2024-06-15', 4),
(3500000.00, '2024-07-01', 6),
(1800000.00, '2024-07-05', 8);

-- 10. Sortants (depenses)
INSERT INTO sortant (montant, date, motif_id) VALUES
(1000000.00, '2024-06-03', 5),
(2000000.00, '2024-06-12', 3),
(250000.00, '2024-06-18', 7),
(1750000.00, '2024-07-02', 6),
(800000.00, '2024-07-06', 2);
