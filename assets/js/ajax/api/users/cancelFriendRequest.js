import api from 'api'
import { FlashMessage } from 'components';
import { reloadPaginationFriendRequestsSent } from './../../ui/pagination/reloadPaginationFriendRequests.js';

$(() => {
    $('.cancel-friend-request').on('click', async function(event) {
        event.preventDefault();
        const id = $(this).attr('data-user-id');

        try {
            await api.delete(`users/friend-request/${id}`).json();
            await reloadPaginationFriendRequestsSent()
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
}) 