const FLASHMESSAGES = $('.flash-messages-container')
const DELAY = 5000;

export class FlashMessage {
    static Types = {
        SUCCESS: 'success',
        WARNING: 'warning',
        ERROR: 'danger',
    };

    constructor(message, type = 'success', generateHTML = true) {
        this.message = message;
        this.type = type;
        this.generateMessage();
    }

    generateMessage() {
        this.element = $(`
            <div class="flash-message alert alert-${this.type} mb-0 p-3"> 
                <p>${this.message}</p>
            </div>
        `);
        FLASHMESSAGES.append(this.element);
        this.element.fadeIn(500);

        this.element.on("click", () => this.removeMessage());

        setTimeout(() => this.removeMessage(), DELAY);
    }

    removeMessage() {
        this.element.fadeOut(500, () => this.element.remove());
    }
}

const raw = localStorage.getItem('flashMessage');
if (raw) {
    const { message, type } = JSON.parse(raw);
    new FlashMessage(message, type);
    localStorage.removeItem('flashMessage');
}

FLASHMESSAGES.children().each(function () {
    const flashText = $(this).text().trim();
    const className = $(this).attr('class'); 
    const match = className.match(/alert-([\w-]+)/);
    $(this).remove();
    new FlashMessage(flashText, match[1]);
});