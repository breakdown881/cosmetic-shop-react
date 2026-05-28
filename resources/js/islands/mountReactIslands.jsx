import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import ReactIslandErrorBoundary from '../components/common/ReactIslandErrorBoundary.jsx';

const parseJsonAttribute = (element, attributeName, fallbackValue) => {
    const rawValue = element.getAttribute(attributeName);

    if (!rawValue) {
        return fallbackValue;
    }

    try {
        return JSON.parse(rawValue);
    } catch (error) {
        console.error(`Invalid JSON in ${attributeName}`, error);
        return fallbackValue;
    }
};

const collectIslandProps = (element) => {
    const props = parseJsonAttribute(element, 'data-props', {});
    const products = parseJsonAttribute(element, 'data-products', null);
    const items = parseJsonAttribute(element, 'data-items', null);

    return {
        ...props,
        ...(products ? { products } : {}),
        ...(items ? { items } : {}),
    };
};

const mountReactIsland = (element, islandRegistry) => {
    if (element.dataset.reactMounted === 'true') {
        return;
    }

    const componentName = element.dataset.reactComponent;
    const Component = islandRegistry[componentName];

    if (!Component) {
        return;
    }

    element.dataset.reactMounted = 'true';

    createRoot(element).render(
        <StrictMode>
            <ReactIslandErrorBoundary componentName={componentName}>
                <Component {...collectIslandProps(element)} />
            </ReactIslandErrorBoundary>
        </StrictMode>,
    );
};

const mountReactIslandsInRoot = (root, islandRegistry) => {
    if (root.matches?.('[data-react-component]')) {
        mountReactIsland(root, islandRegistry);
    }

    root
        .querySelectorAll?.('[data-react-component]')
        .forEach((element) => mountReactIsland(element, islandRegistry));
};

export const mountReactIslands = (islandRegistry) => {
    mountReactIslandsInRoot(document, islandRegistry);

    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    mountReactIslandsInRoot(node, islandRegistry);
                }
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
};
