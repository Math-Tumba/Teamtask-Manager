import api from 'api'
import { addEventListener } from 'utils/dom';
import { modalConfirmation } from 'ajax/ui/modal/modalConfirmation';
import { handleFriendshipAction } from 'ajax/utils/friendshipActionHandler';

addEventListener(document, 'click', async function(e: Event) {
    e.preventDefault();
    const target = this as HTMLElement;
    // const id = target.dataset.userId;
    // if (!id) return;
    
    const confirmed = await modalConfirmation("Voulez-vous vraiment supprimer cet ami ?");
    if (confirmed) {
        handleFriendshipAction(
            target,
            () => api.delete(`users/friends/${target.dataset.userId}`).json(),
        )
    }
}, '.remove-friend');
