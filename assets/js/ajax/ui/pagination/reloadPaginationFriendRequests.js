import ky from 'ky';
import { FlashMessage } from 'components/flashMessage';

export async function reloadPaginationFriendRequestsReceived() {
    const pagination = $('#friend-requests-received-pagination');
    if (!pagination) return;

    try {
        pagination.toggleClass('loading');
        const content = await ky.get('/user/profile/friend-requests/components/pagination-friend-requests-received').text();
        pagination.html(content)
        pagination.toggleClass('loading');
    } catch (error) {
        new FlashMessage("Une erreur est survenue lors du rechargement.", FlashMessage.Types.ERROR);
    }
}

export async function reloadPaginationFriendRequestsSent() {
    const pagination = $('#friend-requests-sent-pagination');
    if (!pagination) return;

    try {
        pagination.toggleClass('loading');
        const content = await ky.get('/user/profile/friend-requests/components/pagination-friend-requests-sent').text();
        pagination.html(content)
        pagination.toggleClass('loading');
    } catch (error) {
        new FlashMessage("Une erreur est survenue lors du rechargement.", FlashMessage.Types.ERROR);
    }
}