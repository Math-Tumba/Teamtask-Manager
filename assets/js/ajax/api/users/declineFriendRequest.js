import api from 'api'
import { FlashMessage } from 'components/flashMessage';
import { getTwigComponent } from 'twigComponents';

$(() => {
    $(document).on('click', '.decline-friend-request', async function(event) {
        event.preventDefault();
        const components = await getTwigComponent($(this));
        const id = $(this).attr('data-user-id');

        try {
            await api.put(`users/friend-requests/${id}/decline`).json();
            await Promise.all(components.map(c => c.render()));       
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
}) 