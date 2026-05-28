import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import AlertMessages from './AlertMessages.jsx';

describe('AlertMessages', () => {
    it('renders success and validation errors', () => {
        render(<AlertMessages message="Saved" type="success" errors={['Name is required']} />);

        expect(screen.getByText('Saved')).toHaveClass('alert-success');
        expect(screen.getByText('Name is required')).toBeInTheDocument();
    });

    it('renders nothing when empty', () => {
        const { container } = render(<AlertMessages />);

        expect(container).toBeEmptyDOMElement();
    });
});
