const CsrfInput = ({ csrfToken }) => (csrfToken ? <input type="hidden" name="_token" value={csrfToken} /> : null);

const ModalCloseButton = () => (
    <button type="button" className="close" data-dismiss="modal" aria-hidden="true">
        ×
    </button>
);

export default function PublicAuthModals({
    captchaHtml = '',
    csrfToken = '',
    facebookLoginUrl = '#',
    forgotPasswordUrl = '#',
    googleLoginUrl = '#',
    labels = {},
    loginUrl = '#',
    registerUrl = '#',
}) {
    return (
        <>
            <div className="modal fade" id="modal-register" role="dialog">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header bg-color">
                            <ModalCloseButton />
                            <h3 className="modal-title text-center">{labels.registerTitle ?? 'Đăng ký'}</h3>
                        </div>
                        <form action={registerUrl} method="post" role="form" name="registration" style={{ fontWeight: 'normal' }}>
                            <CsrfInput csrfToken={csrfToken} />
                            <div className="modal-body">
                                <div className="form-group">
                                    <input type="text" className="form-control" name="name" placeholder={labels.fullname ?? 'Họ và tên'} />
                                </div>
                                <div className="form-group">
                                    <input type="tel" className="form-control" name="mobile" placeholder={labels.mobile ?? 'Số điện thoại'} />
                                </div>
                                <div className="form-group">
                                    <input type="email" className="form-control" name="email" placeholder="Email" />
                                </div>
                                <div className="form-group">
                                    <input
                                        type="password"
                                        className="form-control"
                                        id="password"
                                        name="password"
                                        placeholder={labels.password ?? 'Mật khẩu'}
                                    />
                                </div>
                                <div className="form-group">
                                    <input
                                        type="password"
                                        className="form-control"
                                        name="password_confirmation"
                                        placeholder={labels.passwordConfirmation ?? 'Nhập lại mật khẩu'}
                                    />
                                </div>
                                {!!captchaHtml && (
                                    <div
                                        className="form-group"
                                        dangerouslySetInnerHTML={{
                                            __html: `${captchaHtml}<input type="text" name="hiddenRecaptcha" style="opacity:0;position:absolute;top:0;left:0;height:1px;width:1px;">`,
                                        }}
                                    />
                                )}
                                <input type="hidden" name="reference" value="" />
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-default" data-dismiss="modal">
                                    {labels.cancel ?? 'Hủy'}
                                </button>
                                <button type="submit" className="btn btn-primary">
                                    {labels.registerButton ?? 'Đăng ký'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div className="modal fade" id="modal-login" role="dialog">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header bg-color">
                            <ModalCloseButton />
                            <h3 className="modal-title text-center">{labels.loginTitle ?? 'Đăng nhập'}</h3>
                            <br />
                            <div className="text-center">
                                <a className="btn btn-primary google-login" href={googleLoginUrl}>
                                    <i className="fab fa-google" /> {labels.googleLogin ?? 'Đăng nhập bằng Google'}
                                </a>
                                <a className="btn btn-primary facebook-login" href={facebookLoginUrl}>
                                    <i className="fab fa-facebook-f" /> {labels.facebookLogin ?? 'Đăng nhập bằng Facebook'}
                                </a>
                            </div>
                        </div>
                        <form id="login" action={loginUrl} method="post" role="form">
                            <CsrfInput csrfToken={csrfToken} />
                            <div className="modal-body">
                                <div className="form-group">
                                    <input type="email" name="email" className="form-control" placeholder="Email" required />
                                </div>
                                <div className="form-group">
                                    <input
                                        type="password"
                                        name="password"
                                        className="form-control"
                                        placeholder={labels.password ?? 'Mật khẩu'}
                                        required
                                    />
                                </div>
                                <input type="hidden" name="reference" value="" />
                            </div>
                            <div className="modal-footer">
                                <button type="submit" className="btn btn-primary">
                                    {labels.loginButton ?? 'Đăng Nhập'}
                                </button>
                                <br />
                                <div className="text-left">
                                    <a href="javascript:void(0)" className="btn-register">
                                        {labels.registerPrompt ?? 'Bạn chưa là thành viên? Đăng kí ngay!'}
                                    </a>
                                    <a href="javascript:void(0)" className="btn-forgot-password">
                                        {labels.forgotPassword ?? 'Quên Mật Khẩu?'}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div className="modal fade" id="modal-forgot-password" role="dialog">
                <div className="modal-dialog">
                    <div className="modal-content">
                        <div className="modal-header bg-color">
                            <ModalCloseButton />
                            <h3 className="modal-title text-center">{labels.forgotPasswordTitle ?? 'Quên mật khẩu'}</h3>
                        </div>
                        <form action={forgotPasswordUrl} method="post" role="form">
                            <CsrfInput csrfToken={csrfToken} />
                            <div className="modal-body">
                                <div className="form-group">
                                    <input name="email" type="email" className="form-control" placeholder="Email" required />
                                </div>
                            </div>
                            <div className="modal-footer">
                                <input type="hidden" name="reference" value="" />
                                <button type="submit" className="btn btn-primary">
                                    {labels.send ?? 'GỬI'}
                                </button>
                                <br />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
