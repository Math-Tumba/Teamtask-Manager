import { getComponent } from '@symfony/ux-live-component';

export async function getTwigComponent(jQueryElement) {
    componentElement = document.getElementById(jQueryElement)
    return await getComponent(componentElement);
}