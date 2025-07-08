// Module JS : Intérêts gagnés et graphique
function afficherInterets() {
  const debut = document.getElementById("filtre-debut").value;
  const fin = document.getElementById("filtre-fin").value;
  if (!debut || !fin) {
    alert("Veuillez sélectionner une période de début et de fin.");
    return;
  }
  ajax("GET", `/interets?type_pret_id=1&date_debut=${debut}-01&date_fin=${fin}-28`, null, (data) => {
    const tbody = document.querySelector("#table-interets tbody");
    tbody.innerHTML = "";
    let labels = [], values = [];
    data.forEach(l => {
      tbody.innerHTML += `<tr><td>${l.mois}</td><td>${l.interets}</td></tr>`;
      labels.push(l.mois);
      values.push(l.interets);
    });
    afficherGraphique(labels, values);
  });
}

function afficherGraphique(labels, values) {
  if (window.chartInterets) window.chartInterets.destroy();
  const ctx = document.getElementById("chart-interets").getContext("2d");
  window.chartInterets = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{ label: 'Intérêts gagnés', data: values, backgroundColor: '#4caf50' }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });
}
