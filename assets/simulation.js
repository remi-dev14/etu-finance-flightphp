// Module JS : Simulation de prêt
function afficherGraphiqueAmortissement(data) {
  if (window.amortissementChart) {
    window.amortissementChart.destroy();
  }

  const labels = data.map(d => 'Mois ' + d.mois);
  const capitalRestant = data.map(d => d.capital_restant);
  const interets = data.map(d => d.interet);
  const amortissements = data.map(d => d.amortissement);

  const ctx = document.getElementById('graph-amortissement').getContext('2d');
  window.amortissementChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Capital restant dû',
          data: capitalRestant,
          borderColor: '#2196F3',
          fill: false
        },
        {
          label: 'Intérêts',
          data: interets,
          borderColor: '#F44336',
          fill: false
        },
        {
          label: 'Amortissement',
          data: amortissements,
          borderColor: '#4CAF50',
          fill: false
        }
      ]
    },
    options: {
      responsive: true,
      plugins: {
        title: {
          display: true,
          text: 'Évolution du prêt'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return value.toLocaleString() + ' Ar';
            }
          }
        }
      }
    }
  });
}

function simulerPret() {
  const montant = parseFloat(document.getElementById("sim-montant").value);
  const taux = parseFloat(document.getElementById("sim-taux").value);
  const duree = parseInt(document.getElementById("sim-duree").value);
  const mode = document.getElementById("sim-mode").value;
  const delai = parseInt(document.getElementById("sim-delai").value) || 0;

  if (!montant || !taux || !duree) {
    alert("Veuillez remplir tous les champs obligatoires");
    return;
  }

  let html = '';
  let tableHtml = '<div style="overflow-x:auto;"><table class="amort-table">';
  let totalInterets = 0;
  let totalMensualites = 0;
  let tableauAmortissement = [];

  if (mode === 'constant') {
    // Calcul pour annuités constantes
    const tauxMensuel = taux / 100 / 12;
    const mensualite = (montant * tauxMensuel * Math.pow(1 + tauxMensuel, duree)) / (Math.pow(1 + tauxMensuel, duree) - 1);
    
    tableHtml += `<thead><tr style='background:#e0e0e0;'>
      <th>Mois</th>
      <th>Capital restant dû</th>
      <th>Intérêts</th>
      <th>Amortissement</th>
      <th>Mensualité</th>
    </tr></thead><tbody>`;

    let capitalRestant = montant;
    
    for (let i = 1; i <= duree; i++) {
      const interets = capitalRestant * tauxMensuel;
      const amortissement = mensualite - interets;
      capitalRestant -= amortissement;
      totalInterets += interets;
      totalMensualites += mensualite;

      const ligne = {
        mois: i,
        capital_restant: Math.round(capitalRestant),
        interet: Math.round(interets),
        amortissement: Math.round(amortissement),
        mensualite: Math.round(mensualite)
      };
      tableauAmortissement.push(ligne);

      if (i <= delai) {
        // Pendant le délai, on ne paie que les intérêts
        tableHtml += `<tr style='background:${i%2===0?'#fafafa':'#f0f8ff'};'>
          <td>Mois ${i}</td>
          <td>${Math.round(montant).toLocaleString()} Ar</td>
          <td>${Math.round(interets).toLocaleString()} Ar</td>
          <td>0 Ar</td>
          <td>${Math.round(interets).toLocaleString()} Ar</td>
        </tr>`;
      } else {
        tableHtml += `<tr style='background:${i%2===0?'#fafafa':'#f0f8ff'};'>
          <td>Mois ${i}</td>
          <td>${Math.round(capitalRestant).toLocaleString()} Ar</td>
          <td>${Math.round(interets).toLocaleString()} Ar</td>
          <td>${Math.round(amortissement).toLocaleString()} Ar</td>
          <td>${Math.round(mensualite).toLocaleString()} Ar</td>
        </tr>`;
      }
    }
    
    html = `<b>Mensualité :</b> ${Math.round(mensualite).toLocaleString()} Ar<br>`;

  } else {
    // Mode dégressif (amortissement constant)
    const amortissementConstant = montant / duree;
    const tauxMensuel = taux / 100 / 12;
    
    tableHtml += `<thead><tr style='background:#e0e0e0;'>
      <th>Mois</th>
      <th>Capital restant dû</th>
      <th>Intérêts</th>
      <th>Amortissement</th>
      <th>Mensualité</th>
    </tr></thead><tbody>`;

    let capitalRestant = montant;
    
    for (let i = 1; i <= duree; i++) {
      const interets = capitalRestant * tauxMensuel;
      const mensualite = amortissementConstant + interets;
      totalInterets += interets;
      totalMensualites += mensualite;

      const ligne = {
        mois: i,
        capital_restant: Math.round(capitalRestant),
        interet: Math.round(interets),
        amortissement: Math.round(amortissementConstant),
        mensualite: Math.round(mensualite)
      };
      tableauAmortissement.push(ligne);

      if (i <= delai) {
        // Pendant le délai, on ne paie que les intérêts
        tableHtml += `<tr style='background:${i%2===0?'#fafafa':'#f0f8ff'};'>
          <td>Mois ${i}</td>
          <td>${Math.round(montant).toLocaleString()} Ar</td>
          <td>${Math.round(interets).toLocaleString()} Ar</td>
          <td>0 Ar</td>
          <td>${Math.round(interets).toLocaleString()} Ar</td>
        </tr>`;
      } else {
        tableHtml += `<tr style='background:${i%2===0?'#fafafa':'#f0f8ff'};'>
          <td>Mois ${i}</td>
          <td>${Math.round(capitalRestant).toLocaleString()} Ar</td>
          <td>${Math.round(interets).toLocaleString()} Ar</td>
          <td>${Math.round(amortissementConstant).toLocaleString()} Ar</td>
          <td>${Math.round(mensualite).toLocaleString()} Ar</td>
        </tr>`;
        capitalRestant -= amortissementConstant;
      }
    }
    
    html = `<b>1ère mensualité :</b> ${Math.round(amortissementConstant + (montant * tauxMensuel)).toLocaleString()} Ar<br>`;
    html += `<b>Dernière mensualité :</b> ${Math.round(amortissementConstant + (amortissementConstant * tauxMensuel)).toLocaleString()} Ar<br>`;
  }

  html += `<b>Intérêts totaux :</b> ${Math.round(totalInterets).toLocaleString()} Ar<br>`;
  html += `<b>Coût total :</b> ${Math.round(montant + totalInterets).toLocaleString()} Ar<br>`;

  tableHtml += '</tbody></table></div>';
  document.getElementById("sim-result").innerHTML = html + tableHtml;
  document.getElementById("btn-pdf").style.display = "inline-block";

  // Stockage des résultats pour le PDF
  window._lastSimulation = {
    montant: montant,
    taux: taux,
    duree: duree,
    mode: mode,
    delai: delai,
    interets_total: Math.round(totalInterets),
    cout_total: Math.round(montant + totalInterets),
    mensualites: Math.round(totalMensualites / duree)
  };

  // Afficher le graphique
  afficherGraphiqueAmortissement(tableauAmortissement);

  // Style du tableau
  if (!document.getElementById('amort-table-style')) {
    const style = document.createElement('style');
    style.id = 'amort-table-style';
    style.innerHTML = `
      .amort-table { border-collapse: collapse; width: 100%; margin-top: 15px; font-size: 15px; }
      .amort-table th, .amort-table td { border: 1px solid #bdbdbd; padding: 6px 10px; text-align: right; }
      .amort-table th { background: #e0e0e0; color: #222; text-align: center; }
      .amort-table tr:hover { background: #ffe082 !important; }
    `;
    document.head.appendChild(style);
  }
}


function genererPDF() {
  const infos = {
    client: "Simulation",
    montant: document.getElementById("sim-montant").value,
    taux_annuel: document.getElementById("sim-taux").value,
    duree: document.getElementById("sim-duree").value,
    mode: document.getElementById("sim-mode").value
  };
  ajax("POST", "/pret/pdf", `simulation=${encodeURIComponent(JSON.stringify(window._lastSimulation))}&infos=${encodeURIComponent(JSON.stringify(infos))}`, (res) => {
    window.open(res.pdf, "_blank");
  });
}
