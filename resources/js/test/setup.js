import '@testing-library/jest-dom/vitest';

if (!URL.createObjectURL) {
    URL.createObjectURL = () => 'blob:test-preview';
}

if (!URL.revokeObjectURL) {
    URL.revokeObjectURL = () => {};
}
