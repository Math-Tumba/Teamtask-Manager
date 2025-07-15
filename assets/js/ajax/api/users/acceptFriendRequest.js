import api from 'api'
import { FlashMessage } from 'components/flashMessage';
import { getComponent } from '@symfony/ux-live-component';

$(() => {
    $(document).on('click', '.accept-friend-request', async function(event) {
        const componentElement = document.getElementById('friend-requests-received-pagination');
        const component = await getComponent(componentElement);

        const sentComponentElement = document.getElementById('friend-requests-sent-pagination');
        const sentComponent = await getComponent(sentComponentElement);

        event.preventDefault();
        const id = $(this).attr('data-user-id');

        try {
            await api.put(`users/friend-request/${id}/accept`).json();
            await Promise.all([
                component?.render(),
                sentComponent?.render()
            ]);
        } catch (error) {
            new FlashMessage(error.message, FlashMessage.Types.ERROR);
        }
    });
})