import api from 'api'
import { FlashMessage, FlashMessageType } from 'components/flashMessage';
import { getTwigComponent } from 'utils/twigComponents';
import { modalConfirmation } from 'ajax/ui/modal/modalConfirmation';
import { addEventListener } from 'utils/dom';

addEventListener(document, 'click', async function(e: Event) {
    e.preventDefault();
    const target = this as HTMLElement;
    const id = target.dataset.userId;
    if (!id) return;
    
    const confirmed = await modalConfirmation("Voulez-vous vraiment supprimer cet ami ?");
    if (confirmed) {

        const components = await getTwigComponent(target)
        
        try {
            await api.delete(`users/friends/${id}`).json();
            await Promise.all(components.map(c => c.render()));
        } catch (error) {
            new FlashMessage(error.message, FlashMessageType.ERROR);
        }
    }
}, '.remove-friend');
