import { FlashMessage, FlashMessageType } from 'components/flashMessage';
import { getTwigComponent } from 'utils/twigComponents';

export async function handleFriendshipAction(
    target: HTMLElement, 
    action: () => Promise<any>, 
    successMessage?: string |null,
) {
    const id = target.dataset.userId;
    if (!id) return;

    const components = await getTwigComponent(target);
    try {
        await action();
        if (successMessage) {
            new FlashMessage(successMessage, FlashMessageType.SUCCESS);
        }
        await Promise.all(components.map(c => c.render()));
    } catch (err) {
        new FlashMessage(err.message, FlashMessageType.ERROR);
    }
}