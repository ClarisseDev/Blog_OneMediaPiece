import { myFetch } from './fetch.js';
import { AuthenticationException } from './exceptions.js';

//Avec les module on doit attacher la fonction accueil à la fenêtre du DOM

window.accueil = accueil;
window.closeModal = closeModal;

function closeModal()
{
    document.getElementById("modalFormContainer").innerHTML = "";
    document.getElementById("myModal").style.display = "none";
}

export function accueil()
{
    manageLoginArea();
}

function manageLoginArea()
{
    // On va appeler le serveur pour consulter l'état de la session
    // Et afficher un module de login/logout en conséquence
    myFetch(null, afficheLoginZone, 'api.php?route=Session', 'GET');

}

// Zone de connexion/déconnexion
const afficheLoginZone = function(sessionInfo) {
    console.log(sessionInfo);
    const loginArea = document.getElementById("loginArea");
    loginArea.innerHTML = "";  // Nettoyage complet
    if (sessionInfo.isLogged)
    {
        // console.log("Faire la partie pour se logout");
        let callbackLogout = function(event) { doLogout(); }       // Le callback sur le submit 
        let aHrefLogin = document.createElement('a');
        aHrefLogin.textContent = "Se déconnecter";
        aHrefLogin.id = "buttonLog";
        aHrefLogin.addEventListener('click', function(event)
        {
            event.preventDefault();
            callbackLogout(event);
        });
        loginArea.appendChild(aHrefLogin);
    }
    else
    {
        let callbackLogin = function(event) { doLogin(); }       // Le callback sur le submit 
        let aHrefLogin = document.createElement('a');
        aHrefLogin.textContent = "S'identifier";
        aHrefLogin.id = "buttonLog";
        aHrefLogin.addEventListener('click', function(event)
        {
            event.preventDefault();
            callbackLogin(event);
        });
        loginArea.appendChild(aHrefLogin);

        /*********************************************************************************************** */
        let callbackRegister = function(event) { doRegister(); }       // Le callback sur le submit 
        let aHrefRegister = document.createElement('a');
        aHrefRegister.textContent = "Créer un compte";
        aHrefRegister.id = "buttonRegister";
        aHrefRegister.addEventListener('click', function(event)
        {
            event.preventDefault();
            callbackRegister(event);
        });
        loginArea.appendChild(aHrefRegister);

        /*************************************************************************************************** */
    }

    // Bouton de déconnexion
    function doLogout()
    {
        const dataCallback = function(data)
        {
            accueil();
        }
        myFetch(null, dataCallback, "api.php?route=Logout", "GET");
    }

    // Formulaire de connexion Login
    function doLogin()
    {
        const container = prepareModal();           // nettoyage complet
        const form = document.createElement("form");
        form.id = "loginForm";

        const emailInput = document.createElement("input");
        emailInput.type = "email";
        emailInput.name = "login";
        emailInput.placeholder = "Email";
        emailInput.setAttribute("required", "");
        form.appendChild(emailInput);

        const passwordInput = document.createElement("input");
        passwordInput.type = "password";
        passwordInput.name = "password";
        passwordInput.placeholder = "Mot de passe";
        passwordInput.setAttribute("required", "");
        form.appendChild(passwordInput);

        // ROUTE
        const routeInput = document.createElement("input");
        routeInput.type = "hidden";
        routeInput.name = "route";
        routeInput.value = "Login";
        form.appendChild(routeInput);

        const submitButton = document.createElement("button");
        submitButton.type = "submit";
        submitButton.textContent = "Se connecter";
        form.appendChild(submitButton);
        form.addEventListener("submit", function(event)
        {
            // Désactiver le comportement par défaut
            event.preventDefault();

            // Appel de la fetch API en POST 
            const dataCallback = function(data)
            {
                closeModal();
                accueil();
            }
            const errorCallback = function(error)
            {
                console.log(error.message);
                if ("Invalid Credential" == error.message)
                {
                    alert("Votre login ou password est invalide");
                }
            }
            myFetch(new FormData(form), dataCallback, "api.php", "POST", errorCallback);
            // solution 2 myFetch(new FormData(form), dataCallback, form.action, form.method);
        });
        container.appendChild(form);
    }


    /***************************************************************************************************************** */

    // Formulaire de création de compte
    function doRegister()
    {
        const container = prepareModal();           // nettoyage complet
        const form = document.createElement("form");
        form.id = "registerForm";

        const emailInput = document.createElement("input");
        emailInput.type = "email";
        emailInput.name = "login";
        emailInput.placeholder = "Email";
        emailInput.setAttribute("required", "");
        form.appendChild(emailInput);

        const passwordInput = document.createElement("input");
        passwordInput.type = "password";
        passwordInput.name = "password";
        passwordInput.placeholder = "Mot de passe";
        passwordInput.setAttribute("required", "");
        form.appendChild(passwordInput);

        const pseudoInput = document.createElement("input");
        pseudoInput.type = "text";
        pseudoInput.name = "pseudo";
        pseudoInput.placeholder = "Pseudo";
        pseudoInput.setAttribute("required", "");
        form.appendChild(pseudoInput);

        // ROUTE
        const routeInput = document.createElement("input");
        routeInput.type = "hidden";
        routeInput.name = "route";
        routeInput.value = "Compte";
        form.appendChild(routeInput);

        const submitButton = document.createElement("button");
        submitButton.type = "submit";
        submitButton.textContent = "Créer un compte";
        form.appendChild(submitButton);
        form.addEventListener("submit", function(event)
        {
            // Désactiver le comportement par défaut
            event.preventDefault();

            // Appel de la fetch API en POST 
            const dataCallback = function(data)
            {
                closeModal();
                accueil();
            }
            const errorCallback = function(error) {
                console.log(error.message);
                if (("'login_UNIQUE' - already exists" == error.message)||("'pseudo_UNIQUE' - already exists" == error.message))
                {
                    alert("Votre login ou pseudo existe déjà");
                }
            };
            myFetch(new FormData(form), dataCallback, "api.php", "POST", errorCallback);
            // solution 2 myFetch(new FormData(form), dataCallback, form.action, form.method);
        });
        container.appendChild(form);
    }

    /******************************************************************************************************************** */

    function prepareModal()
    {
        document.getElementById("myModal").style.display = "block";
        const container = document.getElementById("modalFormContainer");
        container.innerHTML = '';
        return container;
    }


};


