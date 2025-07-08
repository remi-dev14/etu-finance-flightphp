<?php
// Helper pour la génération de PDF (ex: plan d'amortissement)
require_once __DIR__ . '/../vendor/autoload.php'; // Assure que FPDF ou TCPDF est chargé

use FPDF\FPDF;

/**
 * Génère un PDF du plan d'amortissement
 * @param array $simulation Résultat de simulatePret()
 * @param array $infos Infos client/prêt (nom, montant, taux, etc.)
 * @param string $filename Chemin du fichier PDF à générer
 * @return string Chemin du PDF généré
 */
function genererPDFPret($simulation, $infos, $filename = 'pret.pdf') {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Plan d\'amortissement', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Client : ' . $infos['client'], 0, 1);
    $pdf->Cell(0, 10, 'Montant : ' . $infos['montant'] . ' Ar', 0, 1);
    $pdf->Cell(0, 10, 'Taux annuel : ' . $infos['taux_annuel'] . ' %', 0, 1);
    $pdf->Cell(0, 10, 'Durée : ' . $infos['duree'] . ' mois', 0, 1);
    $pdf->Cell(0, 10, 'Assurance : ' . $infos['assurance'] . ' %', 0, 1);
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(20, 10, 'Mois', 1);
    $pdf->Cell(40, 10, 'Montant', 1);
    $pdf->Ln();
    $pdf->SetFont('Arial', '', 12);
    foreach ($simulation['plan'] as $ligne) {
        $pdf->Cell(20, 10, $ligne['mois'], 1);
        $pdf->Cell(40, 10, $ligne['montant'], 1);
        $pdf->Ln();
    }
    $pdf->Output('F', $filename);
    return $filename;
}
