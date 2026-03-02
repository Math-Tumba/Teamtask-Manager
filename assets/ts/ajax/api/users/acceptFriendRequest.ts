import api from 'api'
import { addEventListener } from 'utils/dom';
import { handleFriendshipAction } from 'ajax/utils/friendshipActionHandler';

addEventListener(document, 'click', async function(e: Event) {
    e.preventDefault();
    const target = this as HTMLElement;

    handleFriendshipAction(
        target,
        () => api.put(`users/friend-requests/${target.dataset.userId}`, {json: {
            "status": "accept",
        }}).json(),
    )
}, '.accept-friend-request')