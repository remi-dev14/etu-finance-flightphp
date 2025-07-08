// Fonction pour mettre à jour les statistiques du dashboard
function updateDashboardStats() {
    // Mettre à jour le solde
    ajax("GET", "/fonds/total", "", (res) => {
        document.getElementById("dash-solde").textContent = res.solde.toLocaleString() + " Ar";
    });

    // Nombre de prêts en cours
    ajax("GET", "/pret", "", (res) => {
        document.getElementById("dash-prets-count").textContent = res.length;
    });

    // Nombre de clients
    ajax("GET", "/client", "", (res) => {
        document.getElementById("dash-clients-count").textContent = res.length;
    });

    // Créer le graphique des prêts par mois
    updateDashboardChart();
}

// Fonction pour créer le graphique du dashboard
function updateDashboardChart() {
    const currentDate = new Date();
    const sixMonthsAgo = new Date();
    sixMonthsAgo.setMonth(currentDate.getMonth() - 6);

    // Formatage des dates pour l'API
    const dateDebut = sixMonthsAgo.toISOString().slice(0, 7) + '-01';
    const dateFin = currentDate.toISOString().slice(0, 7) + '-31';

    ajax("GET", `/pret`, "", (prets) => {
        const pretsByMonth = {};
        const labels = [];
        const pretData = [];
        const montantData = [];

        // Créer les 6 derniers mois
        for (let i = 0; i < 6; i++) {
            const date = new Date(currentDate);
            date.setMonth(date.getMonth() - i);
            const monthKey = date.toISOString().slice(0, 7);
            labels.unshift(monthKey);
            pretsByMonth[monthKey] = {
                count: 0,
                montant: 0
            };
        }

        // Compter les prêts par mois
        prets.forEach(pret => {
            const monthKey = pret.date_pret.slice(0, 7);
            if (pretsByMonth[monthKey]) {
                pretsByMonth[monthKey].count++;
                pretsByMonth[monthKey].montant += parseInt(pret.montant);
            }
        });

        // Préparer les données pour le graphique
        labels.forEach(month => {
            pretData.push(pretsByMonth[month].count);
            montantData.push(pretsByMonth[month].montant);
        });

        // Détruire le graphique existant s'il existe
        if (window.dashChart) {
            window.dashChart.destroy();
        }

        // Créer le nouveau graphique
        const ctx = document.getElementById('dash-chart').getContext('2d');
        window.dashChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels.map(l => {
                    const date = new Date(l);
                    return date.toLocaleDateString('fr-FR', { month: 'short', year: 'numeric' });
                }),
                datasets: [{
                    label: 'Nombre de prêts',
                    data: pretData,
                    backgroundColor: 'rgba(33, 150, 243, 0.5)',
                    borderColor: 'rgba(33, 150, 243, 1)',
                    borderWidth: 1,
                    yAxisID: 'y'
                }, {
                    label: 'Montant total (Ar)',
                    data: montantData,
                    type: 'line',
                    borderColor: '#4CAF50',
                    backgroundColor: 'transparent',
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Évolution des prêts sur les 6 derniers mois'
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Nombre de prêts'
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Montant total (Ar)'
                        },
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' Ar';
                            }
                        }
                    }
                }
            }
        });
    });
}

function afficherDashboard() {
    // Solde
    ajax("GET", "/fonds", "", (res) => {
        document.getElementById("dash-solde").textContent = res.solde.toLocaleString() + " Ar";
    });
    // Nombre de prêts
    ajax("GET", "/pret", "", (data) => {
        document.getElementById("dash-prets-count").textContent = data.length;
    });
    // Nombre de clients
    ajax("GET", "/client", "", (data) => {
        document.getElementById("dash-clients-count").textContent = data.length;
    });
}

// Mettre à jour les statistiques au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateDashboardStats();
    afficherDashboard();
    // Mettre à jour toutes les 5 minutes
    setInterval(updateDashboardStats, 5 * 60 * 1000);
});