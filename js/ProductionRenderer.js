export class ProductionRenderer {
    static renderCard(production) {
        // Fallback image if none provided
        const imageUrl = production.imageUrl || 'img/placeholder.jpg';
        const dateStr = production.date ? production.date.toLocaleDateString() : 'TBA';
        
        return `
            <div class="production-card brutalist-card">
                <img src="${imageUrl}" alt="${production.title}" style="max-width:100%; height:auto;">
                <div class="card-content">
                    <h3 class="card-title">${production.title}</h3>
                    <p class="card-date">${dateStr}</p>
                    ${production.description ? `<p class="card-desc">${production.description}</p>` : ''}
                </div>
            </div>
        `;
    }
}
