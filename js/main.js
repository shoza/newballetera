// ProductionModel.js - Single Responsibility: Handles data logic
export class Production {
    constructor(title, dateString, imageUrl) {
        this.title = title;
        this.date = dateString ? new Date(dateString) : null;
        this.imageUrl = imageUrl;
    }

    isPast() {
        if (!this.date) return false; // No date defaults to Upcoming
        return this.date < new Date();
    }
}

// ProductionRenderer.js - Single Responsibility: Handles DOM formatting
export class ProductionRenderer {
    static renderCard(production) {
        return `
            <div class="production-card">
                <h3>${production.title}</h3>
                <!-- Add image and details here -->
            </div>
        `;
    }
}

// ProductionController.js - Organizes flow between Model and View
import { Production } from './ProductionModel.js';
import { ProductionRenderer } from './ProductionRenderer.js';

export class ProductionController {
    constructor(productions) {
        this.productions = productions;
    }

    renderAll() {
        const upcomingGrid = document.getElementById('upcoming-grid');
        const pastGrid = document.getElementById('past-grid');

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