import { Production } from './ProductionModel.js';
import { ProductionRenderer } from './ProductionRenderer.js';

export class ProductionController {
    constructor() {
        this.productions = [];
    }

    async fetchAndRender() {
        try {
            const response = await fetch('/api/productions.php');
            const result = await response.json();

            if (result.status === 'success') {
                this.productions = result.data.map(
                    item => new Production(item.id, item.title, item.date, item.image_url, item.description)
                );
                this.renderAll();
            } else {
                console.error("API Error:", result.message);
            }
        } catch (error) {
            console.error("Failed to fetch productions:", error);
        }
    }

    renderAll() {
        const upcomingGrid = document.getElementById('upcoming-grid');
        const pastGrid = document.getElementById('past-grid');

        if (upcomingGrid) upcomingGrid.innerHTML = '';
        if (pastGrid) pastGrid.innerHTML = '';

        this.productions.forEach(prod => {
            const cardHTML = ProductionRenderer.renderCard(prod);
            if (prod.isPast()) {
                if (pastGrid) pastGrid.innerHTML += cardHTML;
            } else {
                if (upcomingGrid) upcomingGrid.innerHTML += cardHTML;
            }
        });
    }
}
