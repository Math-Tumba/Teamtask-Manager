import api from 'api'
import { FlashMessage } from 'components';

$(() => {
    $('.cancel-friend-request').on('click', async function(event) {
        event.preventDefault();
        const id = $(this).attr('data-user-id');

        try {
            await api.delete(`users/friend-request/${id}`).json();
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
}) 