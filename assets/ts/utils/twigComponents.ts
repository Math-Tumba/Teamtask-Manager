import { getComponent } from '@symfony/ux-live-component';

/**
 * Retrieve all the twig components associated with a HTML element.
 * 
 * It reads the `data-related-components` attribute from the 
 * element. The attribute must separate the components using a comma.
 * (Exemple : data-related-components="friend-requests-received-pagination, friend-requests-sent-pagination")
 */
export async function getTwigComponent(element: HTMLElement) {
    const relatedComponentsAttr = element.dataset.relatedComponents;
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