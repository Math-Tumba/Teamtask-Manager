import api from 'api'
import { FlashMessage, FlashMessageType } from 'components/flashMessage';
import { addEventListener } from 'utils/dom';
import { getTwigComponent } from 'utils/twigComponents';

addEventListener(document, 'click', async function(e: Event) {
    e.preventDefault();
    const target = this as HTMLElement;
    const id = target.dataset.userId;
    if (!id) return;

    const components = await getTwigComponent(target);

    try {
        await api.put(`users/friend-requests/${id}`, {json: {
            "status": "decline",
        }}).json();
        await Promise.all(components.map(c => c.render()));       
    } catch (error) {
        new FlashMessage(error.message, FlashMessageType.ERROR);
    }
}, '.decline-friend-request');
