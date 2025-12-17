import api from 'api'
import { FlashMessage, FlashMessageType } from 'components/flashMessage';
import { addEventListener } from 'utils/dom';
import { getTwigComponent } from 'utils/twigComponents';

document.addEventListener("DOMContentLoaded", () => {
    addEventListener(document, 'click', async function(e: Event) {
        e.preventDefault();
        const target = e.target as HTMLElement;
        const components = await getTwigComponent(target);
        const id = target.getAttribute('data-user-id');
        if (!id) return;
        
        try {
            await api.post(`users/friend-requests/${id}`).json();
            new FlashMessage('Demande envoyée.');
            await Promise.all(components.map(c => c.render()));
        } catch (error) {
            new FlashMessage(error.message, FlashMessageType.ERROR);
        }
    }, '.send-friend-request');
}) 