// Module JS : Type de prêt

function chargerTypesPret() {
  ajax("GET", "/typepret", null, (data) => {
    const tbody = document.querySelector("#table-typepret tbody");
    const select = document.getElementById("pret-type");
    tbody.innerHTML = "";
    select.innerHTML = "";
    data.forEach(e => {
      tbody.innerHTML += `<tr><td>${e.id}</td><td>${e.nom}</td><td>${e.taux_annuel}</td><td>${e.montant_min}</td><td>${e.montant_max}</td><td>${e.duree_min}</td><td>${e.duree_max}</td></tr>`;
      select.innerHTML += `<option value="${e.id}">${e.nom} (${e.taux_annuel}%)</option>`;
    });
  });
}


function ajouterTypePret() {
  const nom = document.getElementById("pret-nom").value;
  const taux = document.getElementById("pret-taux").value;
  const montant_min = document.getElementById("pret-montant-min").value;
  const montant_max = document.getElementById("pret-montant-max").value;
  const duree_min = document.getElementById("pret-duree-min").value;
  const duree_max = document.getElementById("pret-duree-max").value;
  const data = `nom=${encodeURIComponent(nom)}&taux_annuel=${encodeURIComponent(taux)}&montant_min=${encodeURIComponent(montant_min)}&montant_max=${encodeURIComponent(montant_max)}&duree_min=${encodeURIComponent(duree_min)}&duree_max=${encodeURIComponent(duree_max)}`;
  ajax("POST", "/typepret", data, chargerTypesPret);
}
