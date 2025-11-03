import { asyncFetchData } from "./utils/fetch.js";

// Fonction de construction de la page d'accueil
export async function buildAccueil() {
    const featuredPosts = document.getElementById('featuredPosts');
    featuredPosts.innerHTML = '<h2>Articles en vedette</h2>';

    try {
        // Appel à ton contrôleur via l’API
        const formData = { route: 'ArticlesAllGet' };
        const articles = await asyncFetchData(formData, { url: 'api.php', method: 'GET' });

        // Si aucun article n’est trouvé
        if (!articles || articles.length === 0) {
            featuredPosts.innerHTML += '<p>Aucun article disponible pour le moment.</p>';
            return;
        }

        // Conteneur pour les articles
        const container = document.createElement('div');
        container.classList.add('articles-container');

        // Boucle sur les articles
        articles.forEach(article => {
            const articleDiv = document.createElement('div');
            articleDiv.classList.add('article-card');

            // Titre
            const title = document.createElement('h3');
            title.textContent = article.titre || 'Sans titre';
            articleDiv.appendChild(title);

            // Contenu abrégé (200 caractères max)
            const content = document.createElement('p');
            const text = article.contenu || '';
            content.textContent = text.length > 200 ? text.substring(0, 200) + '...' : text;
            articleDiv.appendChild(content);

            // Auteur + date
            const meta = document.createElement('small');
            const date = new Date(article.date_creation).toLocaleDateString('fr-FR');
            meta.textContent = `Publié par ${article.auteur || 'Anonyme'} le ${date}`;
            articleDiv.appendChild(meta);

            container.appendChild(articleDiv);
        });

        featuredPosts.appendChild(container);

    } catch (error) {
        console.error('Erreur lors du chargement des articles :', error);
        featuredPosts.innerHTML += '<p class="textRed">Impossible de charger les articles.</p>';
    }
}
