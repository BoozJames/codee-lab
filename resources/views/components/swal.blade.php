<script>
    document.addEventListener('DOMContentLoaded', function () {
        const swalTrigger = document.querySelector('[data-swal-trigger]');
        if (swalTrigger) {
            swalTrigger.addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ $title }}',
                    text: '{{ $text }}',
                    icon: '{{ $icon }}',
                    showCancelButton: true,
                    confirmButtonText: '{{ $confirmButtonText }}',
                    cancelButtonText: '{{ $cancelButtonText }}',
                }).then((result) => {
                    if (result.isConfirmed) {
                        const form = swalTrigger.closest('form');
                        if (form) {
                            form.submit();
                        }
                    }
                });
            });
        }
    });
</script>
