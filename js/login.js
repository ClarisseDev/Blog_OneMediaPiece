import { createInput, createForm, createButton, createH2, createH5, createDiv } from './utils/dom.js';
import { prepareModal, cleanAndCloseModal } from './utils/modal.js';
import { formFetch } from './utils/fetch.js';
import { buildAccueil } from './accueil.js';
import { clearSessionInfo } from './utils/env.js';

// Message d'erreur pour les identifiants incorrects
const wrongCredentialLabel = createH5("Les identifiants ne sont pas connus", { id: "wrongCredential", classes: "textCenter modalTitle textRed" });

// Fonction pour afficher le formulaire de connexion
export function displayConnectionForm() {
    prepareModal((container) => {
        const title = createH2("Se connecter", { classes: "textCenter modalTitle" });
        container.appendChild(title);

        const form = createForm({ method: "POST", classes: "verticalizeCenter" });
        const emailInput = createInput("email", "email", { placeholder: "Email", classes: "formWidth200px" });
        emailInput.addEventListener('focus', removeErrorLabel);
        form.appendChild(emailInput);

        const passwordInput = createInput("password", "pwd", { placeholder: "Mot de passe", classes: "formWidth200px" });
        passwordInput.addEventListener('focus', removeErrorLabel);
        form.appendChild(passwordInput);

        const routeInput = createInput("hidden", "route", { value: "Login" });
        form.appendChild(routeInput);

        const submitButton = createButton("submit", "Se connecter");
        form.appendChild(submitButton);

        form.addEventListener("submit", function(event) {
            event.preventDefault();
            formFetch(form, function(data) {
                if (data === 0) { // Échec du login
                    form.appendChild(wrongCredentialLabel);
                } else {
                    cleanAndCloseModal();
                    buildAccueil();
                }
            });
        });

        container.appendChild(form);
    });
}

// Fonction pour afficher le formulaire d'inscription
export function displayRegistrationForm() {
    prepareModal((container) => {
        const title = createH2("Créer un compte", { classes: "textCenter modalTitle" });
        container.appendChild(title);

        const form = createForm({ method: "POST", classes: "verticalizeCenter" });
        const emailInput = createInput("email", "email", { placeholder: "Email", classes: "formWidth200px" });
        form.appendChild(emailInput);

        const pseudoInput = createInput("text", "pseudo", { placeholder: "Pseudo", classes: "formWidth200px" });
        form.appendChild(pseudoInput);

        const passwordInput = createInput("password", "pwd", { placeholder: "Mot de passe", classes: "formWidth200px" });
        form.appendChild(passwordInput);

        const routeInput = createInput("hidden", "route", { value: "Compte" });
        form.appendChild(routeInput);

        const submitButton = createButton("submit", "Créer un compte");
        form.appendChild(submitButton);

        form.addEventListener("submit", function(event) {
            event.preventDefault();
            formFetch(form, function(data) {
                if (data === 0) { // Échec de l'inscription
                    const errorLabel = createH5("Erreur lors de l'inscription", { classes: "textCenter modalTitle textRed" });
                    form.appendChild(errorLabel);
                } else {
                    cleanAndCloseModal();
                    buildAccueil();
                }
            });
        });

        container.appendChild(form);
    });
}

// Fonction pour supprimer le message d'erreur
function removeErrorLabel() {
    wrongCredentialLabel.remove();
}

// Fonction pour configurer les boutons de connexion/déconnexion
export function setupAuthButtons(isLogged) {
    let myLoginArea = document.getElementById('myLoginArea');
    let loginArea = createDiv({ id: 'loginArea', classes: 'loginArea' });
    myLoginArea.appendChild(loginArea);
    loginArea.innerHTML = '';

    if (isLogged) {
        const logoutButton = createButton("button", "Se déconnecter", { classes: "buttonClass" });
        logoutButton.addEventListener("click", function() {
            clearSessionInfo();
            buildAccueil();
        });
        loginArea.appendChild(logoutButton);
    } else {
        const loginButton = createButton("button", "Se connecter", { classes: "buttonClass" });
        loginButton.addEventListener("click", displayConnectionForm);
        loginArea.appendChild(loginButton);

        const registerButton = createButton("button", "Créer un compte", { classes: "buttonClass" });
        registerButton.addEventListener("click", displayRegistrationForm);
        loginArea.appendChild(registerButton);
    }
}
