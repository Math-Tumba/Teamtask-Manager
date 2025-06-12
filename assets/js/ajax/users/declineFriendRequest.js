import api from 'api'
import { FlashMessage } from 'components';

$(() => {
    $('.decline-friend-request').on('click', async function(event) {
        event.preventDefault();
        const id = $(this).attr('data-user-id');

        try {
            await api.put(`users/friend-request/${id}/decline`).json();
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
}) 