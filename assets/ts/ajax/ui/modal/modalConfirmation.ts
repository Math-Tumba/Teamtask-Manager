import { addEventListener, generateElements } from 'utils/dom'

export async function modalConfirmation(message = "Voulez-vous vraiment effectuer cette action ?") {
    return new Promise((resolve) => {
        const modal = generateElements(`
            <div class="modal modal-confirmation">
                <div class="overlay"></div>
                <div class="modal-content">
                    <p class="modal-close">X</p>
                    <p>${message}</p>
                    <div class="actions d-flex gap-3">
                        <button class="btn btn-success" type="button">Confirmer</button>
                        <button class="btn btn-danger" type="button">Annuler</button>
                    </div>
                </div>
            </div>
        `);

        document.body.append(modal);

        addEventListener(modal, 'click', function() {
            modal.remove();
            resolve(true);
        }, '.btn-success');

        addEventListener(modal, 'click', function() {
            modal.remove();
            resolve(false);
        }, '.btn-danger, .modal-close, .overlay');
    })
}