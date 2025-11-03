import { buildAccueil } from './accueil.js';
import { setupAuthButtons, displayConnectionForm } from './login.js';
import { getSessionInfo } from "./utils/env.js";
import { cleanAndCloseModal } from './utils/modal.js';

// Fonction de fermeture de la modale
export function closeModal() {
    cleanAndCloseModal();
}

//Vérification de la session utilisateur
function manageSession(sessionInfo) {
    if (sessionInfo.isLogged) {
        console.log("Utilisateur connecté, chargement de l'application...");
        buildAccueil();
    } else {
        console.log("Utilisateur non connecté");
        setupAuthButtons();
    }
};

//Fonction d'initialisation de la page d'accueil
export async function accueil() {
    console.log("Exécution de la page accueil");
    let sessionInfo = await getSessionInfo();
    manageSession(sessionInfo);
}

// Rendre les fonctions accessibles depuis l'extérieur
window.accueil = accueil;
window.closeModal = closeModal;