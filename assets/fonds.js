function chargerMotifs() {
    ajax("GET", "/motif", "", (data) => {
        const select = document.getElementById("fonds-motif");
        select.innerHTML = '<option value="">Choisir un motif</option>';
        data.forEach(motif => {
            select.innerHTML += `<option value="${motif.id}">${motif.motif}</option>`;
        });
    });
}

document.addEventListener("DOMContentLoaded", chargerMotifs);

function ajouterFonds() {
    const montant = parseFloat(document.getElementById("fonds-montant").value);
    const type = document.getElementById("fonds-type").value;
    const motif_id = document.getElementById("fonds-motif").value;

    if (!montant || !motif_id) {
        alert("Veuillez remplir tous les champs");
        return;
    }
    if (montant <= 0) {
        alert("Le montant doit être supérieur à 0");
        return;
    }

    if (type === 'entrant') {
        ajouterEntrant(montant, motif_id);
    } else {
        ajouterSortant(montant, motif_id);
    }
}

function ajouterEntrant(montant, motif_id) {
    const data = `montant=${montant}&motif_id=${motif_id}`;
    ajax("POST", "/entrant", data, (res) => {
        if (res.message) {
            alert("Entrée de fonds ajoutée !");
            document.getElementById("fonds-montant").value = "";
            document.getElementById("fonds-motif").value = "";
            afficherFonds();
        } else {
            alert(res.error || "Erreur lors de l'opération");
        }
    });
}

function ajouterSortant(montant, motif_id) {
    const data = `montant=${montant}&motif_id=${motif_id}`;
    ajax("POST", "/sortant", data, (res) => {
        if (res.message) {
            alert("Sortie de fonds ajoutée !");
            document.getElementById("fonds-montant").value = "";
            document.getElementById("fonds-motif").value = "";
            afficherFonds();
        } else {
            alert(res.error || "Erreur lors de l'opération");
        }
    });
}

function afficherFonds() {
    // PAS de /ws ici, car api = '/ws' dans app.js
    ajax("GET", "/fonds", "", (res) => {
        document.getElementById("fonds-solde").textContent = res.solde.toLocaleString() + " Ar";
        document.getElementById("fonds-entrees").textContent = res.entrees.toLocaleString() + " Ar";
        document.getElementById("fonds-sorties").textContent = res.sorties.toLocaleString() + " Ar";
        document.getElementById("fonds-prets").textContent = res.prets.toLocaleString() + " Ar";
    });
}

// Appel initial
afficherFonds();