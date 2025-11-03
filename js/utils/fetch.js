import { AuthenticationException } from './exceptions.js';

const defaultResponseCallback = function(response) {
    if (!response.ok) {
        console.log("Status Text : " + response.statusText + " || code : " + response.status);
        let statusText = response.statusText;        // on attend que le texte soit complètement reçu
        let split = statusText.split(":");
        if (split.length == 2)
        {
            statusText = split[1].trim();      // Tableau numéroté à partir de zéro
        }
        else
        {
            statusText = "Une erreur est survenue (message fetch)";            // Rien à extraire
        }

//        console.log("[" + statusText + "]");          // A supprimer quand on sera sûr

        switch (response.status)
        {
            case 499 : throw new AuthenticationException(statusText);
            default : throw new Error('Network response was not ok ' + response.statusText);
        }
    }
    return response.json();
}

const defaultDataCallback = function(data) {
    console.log("Data :\n", data);
}

const defaultErrorCallback = function(error) {
    console.error('Erreur:', error);
}

// Fonction de fetch personnalisée
export function myFetch(formData, dataCallback, url, method, errorCallback = null, responseCallback = null, contentType = 'application/x-www-form-urlencoded') {
    fetch(url, {
        method: method,
        headers: { 'Content-Type': contentType },
        body: method == 'GET' || method == 'DELETE' ? null : new URLSearchParams(formData).toString() // DELETE et GET n'ont pas de body, les paramètres passent dans l'URL
    })
    .then(response => (responseCallback == null ? defaultResponseCallback(response) : responseCallback(response) ) )
    .then(data => (dataCallback == null ? defaultDataCallback(data) : dataCallback(data) ))
    .catch(error => (errorCallback == null ? defaultErrorCallback(error): errorCallback(error) ));
}

// Fonction de fetch asynchrone personnalisée
export async function asyncFetchData(formData, { url = "api.php", method = "GET", errorCallback = null, responseCallback = null, contentType = 'application/x-www-form-urlencoded',
    container = null, errorLabel = null
 } = {} ) {
    let sendUrl = url;
    let sendBody = null;
    if (method == 'GET' || method == 'DELETE') { // DELETE et GET n'ont pas de body, les paramètres passent dans l'URL
        sendUrl = url + "?" + new URLSearchParams(formData).toString();
    } else {
        sendBody = new URLSearchParams(formData).toString();
    }
    const response = await fetch(sendUrl, {
        method: method,
        headers: { 'Content-Type': contentType },
        body: sendBody  
    });
    if ( ! response.ok) {  // Vérifie si la réponse est réussie (status 2xx)
        try {
            await manageNotOk(response);
        } catch (ex) {
            defaultFormErrorCallback(ex, container, errorLabel);
        }
        throw new Error(`Erreur HTTP : ${response.status}`);
    }
    const data = await response.json();
    return data;
}

export function formFetch(form, dataCallback, { errorCallback = null, responseCallback = null } = {} ) { 
    myFetch(new FormData(form), dataCallback, 
        { url : form.action, method : form.method, errorCallback : errorCallback, responseCallback : responseCallback, contentType : form.enctype } );
}