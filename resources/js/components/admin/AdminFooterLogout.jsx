export default function AdminFooterLogout({ csrfToken = '', labels = {}, logoutUrl = '#' }) {
    return (
        <>
            <footer className="sticky-footer">
                <div className="container my-auto">
                    <div className="copyright text-center my-auto">
                        <span>{labels.copyright ?? 'Copyright ©'}</span>
                    </div>
                </div>
            </footer>

            <a className="scroll-to-top rounded" href="#page-top">
                <i className="fas fa-angle-up" />
            </a>

            <div className="modal fade" id="logoutModal" tabIndex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title" id="exampleModalLabel">
                                {labels.exitConfirm ?? 'Exit?'}
                            </h5>
                            <button className="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div className="modal-footer">
                            <button className="btn btn-secondary" type="button" data-dismiss="modal">
                                {labels.cancel ?? 'Cancel'}
                            </button>
                            <form action={logoutUrl} method="post">
                                {csrfToken && <input type="hidden" name="_token" value={csrfToken} />}
                                <button type="submit" className="btn btn-primary">
                                    {labels.exit ?? 'Exit'}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
