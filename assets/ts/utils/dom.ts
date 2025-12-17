type EventHandler<T extends Event = Event> = (
    this: HTMLElement | Document | Window, 
    event: T
) => unknown;

export function generateElements(html: string): HTMLElement {
    const template = document.createElement('template');
    template.innerHTML = html.trim();
    return template.content.firstElementChild as HTMLElement
} 

export function fadeIn(element: HTMLElement): void {
    element.classList.replace('hide', 'show');
}

export function fadeOut(element: HTMLElement): void {
    element.classList.replace('show', 'hide');
}

export function toggle(element: HTMLElement): void {
    if (element.style.display === 'none') {
        element.style.display = '';
    } else {
        element.style.display = '';
    }
}

export function addEventListener(element: HTMLElement | Document | Window, eventName: string, eventHandler: EventHandler, selector?: string) : EventListener {
    let wrappedHandler: EventListener;

    if (selector) {
        wrappedHandler = (e: Event) => {
            if (!e.target) return;

            const delegatedElement: HTMLElement | null = (e.target as HTMLElement).closest(selector);
            if (delegatedElement) {
                eventHandler.call(delegatedElement, e);
            }
        };
    } else {
        wrappedHandler = (e) => {
            eventHandler.call(element, e);
        };
    }
    element.addEventListener(eventName, wrappedHandler);

    return wrappedHandler;
}