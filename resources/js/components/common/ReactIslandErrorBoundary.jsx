import { Component } from 'react';

export default class ReactIslandErrorBoundary extends Component {
    constructor(props) {
        super(props);

        this.state = {
            hasError: false,
        };
    }

    static getDerivedStateFromError() {
        return {
            hasError: true,
        };
    }

    componentDidCatch(error, errorInfo) {
        console.error(`React island "${this.props.componentName}" failed.`, error, errorInfo);
    }

    render() {
        if (this.state.hasError) {
            return null;
        }

        return this.props.children;
    }
}
