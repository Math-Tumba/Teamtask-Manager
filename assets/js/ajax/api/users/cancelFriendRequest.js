import api from 'api'
import { FlashMessage } from 'components/flashMessage';
import { getTwigComponent } from 'twigComponents';
import { getComponent } from '@symfony/ux-live-component';

$(() => {
    $(document).on('click', '.cancel-friend-request', async function(event) {
        const componentElement = document.getElementById('friend-requests-received-pagination');
        const component = await getComponent(componentElement);

        event.preventDefault();
        const id = $(this).attr('data-user-id');
        try {
            await api.delete(`users/friend-request/${id}`).json();
            if (component) {
                await component.render();
            }
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
}) 