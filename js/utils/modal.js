// Fonction pour ouvrir la modal
function openModal() {
    document.getElementById("myModal").style.display = "flex";
}

// Fonction pour fermer la modal
function closeModal() {
    document.getElementById("myModal").style.display = "none";
}

// Ajout d'un écouteur d'événements pour la fermeture de la modal
window.onclick = function (event) {
    const modal = document.getElementById("myModal");
    if (event.target === modal) {
        closeModal();
    }
}

export function cleanAndCloseModal() {
    document.getElementById('myModal').style.display = "none";
    document.getElementById('modalFormContainer').innerHTML = '';
}

/**
 * Ici on passe une fonction en callback, elle va s'occuper de construire le DOM
 * on passe le container en paramètre dans lequel on va ajouter nos éléments DOM
 */
export function prepareModal(guiBuilderCallback) {
    const container = document.getElementById('modalFormContainer');
    container.innerHTML = ''; // Conchita Martinez, fée du logis
	guiBuilderCallback(container);
    openModal();
}