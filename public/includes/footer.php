<!-- Bootstrap + Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    const _toast = <?= json_encode($_SESSION['toast'] ?? null) ?>;
    <?php unset($_SESSION['toast']); ?>
    if (_toast) {
        toastr.options = { positionClass: 'toast-top-right', timeOut: 4000 };
        toastr[_toast.type](_toast.message);
    }
</script>
</body>
</html>
