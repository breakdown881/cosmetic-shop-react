export default function AlertMessages({ errors = [], message = null, type = 'success' }) {
    const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';

    if (!message && !errors.length) {
        return null;
    }

    return (
        <>
            {message && <div className={`alert ${alertClass}`}>{message}</div>}
            {!!errors.length && (
                <div className="alert alert-danger">
                    <ul>
                        {errors.map((error) => (
                            <li key={error}>{error}</li>
                        ))}
                    </ul>
                </div>
            )}
        </>
    );
}
