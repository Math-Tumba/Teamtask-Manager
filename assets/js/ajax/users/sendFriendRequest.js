import api from 'api'
import { FlashMessage } from 'components';

$(() => {
    $(".send-friend-request").on("click", async function(event) {
        event.preventDefault();
        const id = $(this).attr("data-user-id");

        try {
            await api.post(`users/friend-request/${id}`).json();
            new FlashMessage('Demande envoyée.');
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
})