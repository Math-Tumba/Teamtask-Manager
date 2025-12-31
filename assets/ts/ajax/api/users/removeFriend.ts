import api from 'api'
import { FlashMessage, FlashMessageType } from 'components/flashMessage';
import { getTwigComponent } from 'utils/twigComponents';
import { modalConfirmation } from 'ajax/ui/modal/modalConfirmation';
import { addEventListener } from 'utils/dom';

document.addEventListener('DOMContentLoaded', () => {
    addEventListener(document, 'click', async function(e: Event) {
        e.preventDefault();
        const target = e.target as HTMLElement;
        
        const confirmed = await modalConfirmation("Voulez-vous vraiment supprimer cet ami ?");
        if (confirmed) {
            const components = await getTwigComponent(target)
            const id = target.dataset.userId;
            if (!id) return;
            
            try {
                await api.delete(`users/friends/${id}`).json();
                await Promise.all(components.map(c => c.render()));
            } catch (error) {
                new FlashMessage(error.message, FlashMessageType.ERROR);
            }
        }
    }, '.remove-friend');
})