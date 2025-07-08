// Module JS : Prêt

function chargerPrets() {
  ajax("GET", "/pret", null, (data) => {
    const tbody = document.querySelector("#table-pret tbody");
    tbody.innerHTML = "";
    data.forEach(p => {
      tbody.innerHTML += `<tr>
        <td>${p.id}</td>
        <td>${p.client_nom ?? p.client_id}</td>
        <td>${p.type_nom ?? p.type_pret_id}</td>
        <td>${p.montant}</td>
        <td>${p.duree} mois</td>
        <td>${p.date_pret}</td>
        <td><button onclick="voirOperations(${p.id})">Voir opérations</button></td>
      </tr>`;
    });
  });
}

// Fonction globale pour voir les opérations d'un prêt
function voirOperations(pretId) {
  showModule('operations');
  document.getElementById('operations-pret-id').value = pretId;
  if (typeof chargerOperations === 'function') chargerOperations(pretId);
}

function ajouterPret() {
  const client_id = document.getElementById("pret-client").value;
  const type_pret_id = document.getElementById("pret-type").value;
  const montant = document.getElementById("pret-montant").value;
  const duree = document.getElementById("pret-duree").value;
  const assurance = document.getElementById("pret-assurance")?.value || 0;
  const delai = document.getElementById("pret-delai")?.value || 0;
  const data = `client_id=${client_id}&type_pret_id=${type_pret_id}&montant=${montant}&duree=${duree}&assurance=${assurance}&delai=${delai}`;
  ajax("POST", "/pret", data, () => {
    chargerPrets();
  });
}
