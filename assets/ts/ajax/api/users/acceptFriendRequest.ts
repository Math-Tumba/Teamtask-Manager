import api from 'api'
import { FlashMessage, FlashMessageType } from 'components/flashMessage';
import { addEventListener } from 'utils/dom';
import { getTwigComponent } from 'utils/twigComponents';

document.addEventListener('DOMContentLoaded', () => {
    addEventListener(document, 'click', async function(e: Event) {
        e.preventDefault();
        const target = e.target as HTMLElement
        const components = await getTwigComponent(target);
        const id = target.dataset.userId;
        if (!id) return;

        try {
            await api.put(`users/friend-requests/${id}`, {json: {
                "status": "accept",
            }}).json();
            await Promise.all(components.map(c => c.render()));
        } catch (err) {
            new FlashMessage(err.message, FlashMessageType.ERROR)
        }
    }, '.accept-friend-request');
})