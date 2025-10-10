import { getComponent } from '@symfony/ux-live-component';

export async function getTwigComponent(jQueryElement) {
    const relatedComponentsAttr = jQueryElement.attr('data-related-components');
    const components = [];
    
    if (typeof relatedComponentsAttr !== 'undefined') {
        const ids = relatedComponentsAttr.split(',').map(id => id.trim());

        for (const id of ids) {
            const element = document.getElementById(id);
            if (element) {
                const component = await getComponent(element);
                if (component) {
                    components.push(component);
                }
            }
        }
    }
    return components;
}