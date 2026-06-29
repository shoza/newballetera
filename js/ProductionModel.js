export class Production {
    constructor(id, title, dateString, imageUrl, description) {
        this.id = id;
        this.title = title;
        this.date = dateString ? new Date(dateString) : null;
        this.imageUrl = imageUrl;
        this.description = description;
    }

    isPast() {
        if (!this.date) return false; // No date defaults to Upcoming
        return this.date < new Date();
    }
}
