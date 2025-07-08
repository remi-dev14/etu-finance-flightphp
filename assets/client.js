// Module JS : Client

function chargerClients() {
  ajax("GET", "/client", null, (data) => {
    const tbody = document.querySelector("#table-client tbody");
    const select = document.getElementById("pret-client");
    tbody.innerHTML = "";
    select.innerHTML = "";
    data.forEach(e => {
      tbody.innerHTML += `<tr><td>${e.id}</td><td>${e.nom}</td><td>${e.prenom ?? ''}</td><td>${e.cin}</td><td>${e.date_naissance ?? ''}</td></tr>`;
      select.innerHTML += `<option value="${e.id}">${e.nom} ${e.prenom ?? ''} (${e.cin})</option>`;
    });
  });
}


function ajouterClient() {
  const nom = document.getElementById("client-nom").value;
  const prenom = document.getElementById("client-prenom").value;
  const cin = document.getElementById("client-cin").value;
  const date_naissance = document.getElementById("client-date-naissance").value;
  const data = `nom=${encodeURIComponent(nom)}&prenom=${encodeURIComponent(prenom)}&cin=${encodeURIComponent(cin)}&date_naissance=${encodeURIComponent(date_naissance)}`;
  ajax("POST", "/client", data, chargerClients);
}
