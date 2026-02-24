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
        await api.delete(`users/friend-requests/${id}`).json();
        await Promise.all(components.map(c => c.render()));
    } catch (err) {
        new FlashMessage(err.message, FlashMessageType.ERROR);
    }
}, '.cancel-friend-request');
