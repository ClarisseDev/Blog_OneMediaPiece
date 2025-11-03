import { asyncFetchData } from "./fetch.js"; 

let sessionInfo;

export function clearSessionInfo() {
    sessionInfo = null;
}

export function clearCache() {
    log("Nettoyage du cache");
    clearSessionInfo();
}

// Renvoie les informations de session de l'utilisateur courant
export async function getSessionInfo() {
    if (sessionInfo == null) {
        const formData = new FormData();
        formData.append("route", "Session");
        const json = await asyncFetchData(formData);
        sessionInfo = Object.freeze(json);
		console.log("Mise en cache des données de session", { obj : sessionInfo });
    }
    return sessionInfo;
}
