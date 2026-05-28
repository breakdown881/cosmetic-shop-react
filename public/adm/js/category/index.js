$(document).ready(function () {
    $(document).on('click', '.btn-remove', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Warning',
            text: window.translations.confirmDelete,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: window.translations.deleteButton,
            cancelButtonText: window.translations.cancelButton
        }).then((result) => {
            if (result.isConfirmed) {
                $('#delete-form-' + id).submit();
            }
        });
    });

    $(document).on('click', '.btn-change-status', function () {
        const id = $(this).data('id'),
            status = $(this).data('status'),
            url = $(this).data('url');

        Swal.fire({
            title: 'Warning',
            text: window.translations.confirmChangeStatus,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: window.translations.confirmButton,
            cancelButtonText: window.translations.cancelButton
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        'status': status
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        }
                    }
                });
            }
        });
    });
});
