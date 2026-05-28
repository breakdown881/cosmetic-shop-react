import AlertMessages from '../common/AlertMessages.jsx';

export default function AdminLoginPage({
    action,
    alerts = {},
    csrfToken,
    labels = {},
    logoUrl,
}) {
    return (
        <div className="container">
            <AlertMessages {...alerts} />
            <div className="card card-login mx-auto mt-5">
                <div className="card-header card-header-login">
                    <img src={logoUrl} alt={labels.logoAlt ?? 'Admin logo'} />
                </div>
                <div className="card-body">
                    <form action={action} method="post">
                        {csrfToken && <input type="hidden" name="_token" value={csrfToken} />}
                        <div className="form-group">
                            <div className="form-label-group">
                                <input
                                    type="text"
                                    id="username"
                                    name="email"
                                    className="form-control"
                                    placeholder="Email"
                                    required
                                    autoFocus
                                />
                                <label htmlFor="username">{labels.email ?? 'Email'}</label>
                            </div>
                        </div>
                        <div className="form-group">
                            <div className="form-label-group">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    className="form-control"
                                    placeholder="Password"
                                    required
                                />
                                <label htmlFor="password">{labels.password ?? 'Password'}</label>
                            </div>
                        </div>
                        <div className="form-group">
                            <div className="checkbox">
                                <label>
                                    <input type="checkbox" value="remember-me" name="remember-me" />{' '}
                                    {labels.rememberMe ?? 'Remember me'}
                                </label>
                            </div>
                        </div>
                        <button type="submit" className="btn btn-primary btn-block">
                            {labels.login ?? 'Login'}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    );
}
