import { getComponent } from '@symfony/ux-live-component';

/**
 * 
 */
export async function getTwigComponent(element: HTMLElement) {
    const relatedComponentsAttr = element.getAttribute('data-related-components')!;
    const components = [];
    
    if (typeof relatedComponentsAttr !== 'undefined') {
        const componentIds = relatedComponentsAttr.split(',').map(id => id.trim());
        for (const id of componentIds) {
            const componentElement = document.getElementById(id);
            if (componentElement) {
                const component = await getComponent(componentElement);
                if (component) {
                    components.push(component);
                }
            }
        }
    }
    
    return components;
}