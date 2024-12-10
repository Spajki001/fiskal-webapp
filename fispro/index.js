document.addEventListener('DOMContentLoaded', () => {
    const cards = [
        {
            title: "FISPRO Analytics",
            description: "Analizirajte svoje podatke.",
            link: "http://localhost/fispro_analytics",
            image: "https://via.placeholder.com/300x200/000000/FFFFFF"
        },
        {
            title: "FISPRO Inventura",
            description: "Upravljajte svojim inventarom.",
            link: "http://localhost/fispro_inventura",
            image: "https://via.placeholder.com/300x200/000000/FFFFFF"
        },
        {
            title: "FISPRO Knjigovodstvo",
            description: "Vodite svoje knjige.",
            link: "http://localhost/fispro_knjigovodstvo",
            image: "https://via.placeholder.com/300x200/000000/FFFFFF"
        }
    ];

    const cardContainer = document.getElementById('card-container');

    cards.forEach(card => {
        const cardElement = document.createElement('a');
        cardElement.href = card.link;
        cardElement.className = 'col-md-4 mb-4 text-decoration-none';
        cardElement.innerHTML = `
            <div class="card" style="background-image: url('${card.image}');">
                <div class="card-body">
                    <h5 class="card-title">${card.title}</h5>
                    <p class="card-text">${card.description}</p>
                </div>
            </div>
        `;
        cardContainer.appendChild(cardElement);
    });
});