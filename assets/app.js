// JS principal pour la gestion des vues et appels AJAX
const api = "/ws"; // Utilise le sous-dossier ws pour les appels API

function ajax(method, url, data, callback) {
  const xhr = new XMLHttpRequest();
  xhr.open(method, api + url, true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = () => {
    if (xhr.readyState === 4 && xhr.status === 200) {
      callback(JSON.parse(xhr.responseText));
    }
  };
  xhr.send(data);
}

// === TYPE PRET ===
function chargerTypesPret() {
  ajax("GET", "/typepret", null, (data) => {
    const tbody = document.querySelector("#table-typepret tbody");
    const select = document.getElementById("pret-type");
    tbody.innerHTML = "";
    select.innerHTML = "";
    data.forEach(e => {
      tbody.innerHTML += `<tr><td>${e.id}</td><td>${e.nom}</td><td>${e.taux_annuel ?? e.taux}</td></tr>`;
      select.innerHTML += `<option value="${e.id}">${e.nom} (${e.taux_annuel ?? e.taux}%)</option>`;
    });
  });
}

function ajouterTypePret() {
  const nom = document.getElementById("pret-nom").value;
  const taux = document.getElementById("pret-taux").value;
  ajax("POST", "/typepret", `nom=${nom}&taux_annuel=${taux}`, chargerTypesPret);
}

// === CLIENT ===
function chargerClients() {
  ajax("GET", "/client", null, (data) => {
    const tbody = document.querySelector("#table-client tbody");
    const select = document.getElementById("pret-client");
    tbody.innerHTML = "";
    select.innerHTML = "";
    data.forEach(e => {
      tbody.innerHTML += `<tr><td>${e.id}</td><td>${e.nom}</td><td>${e.cin}</td></tr>`;
      select.innerHTML += `<option value="${e.id}">${e.nom} (${e.cin})</option>`;
    });
  });
}

function ajouterClient() {
  const nom = document.getElementById("client-nom").value;
  const cin = document.getElementById("client-cin").value;
  ajax("POST", "/client", `nom=${nom}&cin=${cin}`, chargerClients);
}

// === PRET ===
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
      </tr>`;
    });
  });
}

function ajouterPret() {
  const client_id = document.getElementById("pret-client").value;
  const type_pret_id = document.getElementById("pret-type").value;
  const montant = document.getElementById("pret-montant").value;
  const duree = document.getElementById("pret-duree").value;
  const data = `client_id=${client_id}&type_pret_id=${type_pret_id}&montant=${montant}&duree=${duree}`;
  ajax("POST", "/pret", data, () => {
    chargerPrets();
    chargerEtablissements();
  });
}

// INITIALISATION
chargerTypesPret();
chargerClients();
chargerPrets();
