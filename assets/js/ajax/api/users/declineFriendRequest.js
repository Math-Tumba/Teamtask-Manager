import api from 'api'
import { FlashMessage } from 'components';
import { reloadPaginationFriendRequestsReceived } from './../../ui/pagination/reloadPaginationFriendRequests.js';

$(() => {
    $('.decline-friend-request').on('click', async function(event) {
        event.preventDefault();
        const id = $(this).attr('data-user-id');

        try {
            await api.put(`users/friend-request/${id}/decline`).json();
            await reloadPaginationFriendRequestsReceived();
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
}) 