// Module JS : Opérations de prêt
function chargerOperations(pret_id) {
  if (!pret_id) {
    alert("Veuillez entrer un ID de prêt valide.");
    return;
  }
  ajax("GET", `/operation?pret_id=${pret_id}`, null, (data) => {
    const tbody = document.querySelector("#table-operation tbody");
    tbody.innerHTML = "";
    let now = new Date();
    if (!data || data.length === 0) {
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#888;">Aucune opération trouvée pour ce prêt.</td></tr>';
      return;
    }
    data.forEach(o => {
      // Calcul de la date d'échéance réelle (en tenant compte du délai 1er remboursement)
      let mois = parseInt(o.mois || 1);
      let annee = parseInt(o.annee || new Date().getFullYear());
      let dateEcheance = new Date(annee, mois - 1, 1);
      let statutPaiement = '';
      if (o.statut && o.statut.toLowerCase() === 'remboursé') {
        statutPaiement = '<span style="color:green">Payé</span>';
      } else if (dateEcheance < now) {
        statutPaiement = '<span style="color:red">Retard</span>';
      } else {
        statutPaiement = '<span style="color:orange">À venir</span>';
      }
      let actionBtn = '';
      if (!o.statut || o.statut.toLowerCase() !== 'remboursé') {
        actionBtn = `<button onclick="payerOperation(${o.id}, ${pret_id})">Payer</button>`;
      }
      tbody.innerHTML += `<tr>
        <td>${o.mois ?? ''}</td>
        <td>${o.annee ?? ''}</td>
        <td>${o.emprunt_restant ?? ''}</td>
        <td>${o.interet_mensuel ?? ''}</td>
        <td>${o.montant_rembourse ?? ''}</td>
        <td>${o.echeance ?? ''}</td>
        <td>${o.valeur_note ?? ''}</td>
        <td>${statutPaiement} ${actionBtn}</td>
      </tr>`;
    });
  });
}

// Action de paiement d'une opération
function payerOperation(id, pret_id) {
  if (!confirm('Confirmer le paiement de cette opération ?')) return;
  ajax('PUT', `/operation/${id}/pay`, null, function() {
    chargerOperations(pret_id);
  });
}
