import { handleFriendshipAction } from 'ajax/utils/friendshipActionHandler';
import api from 'api'
import { addEventListener } from 'utils/dom';

addEventListener(document, 'click', async function(e: Event) {
    e.preventDefault();
    const target = this as HTMLElement;

    handleFriendshipAction(
        target,
        () => api.post(`users/friend-requests/${target.dataset.userId}`).json(),
        'Demande envoyée'
    );
}, '.send-friend-request');
