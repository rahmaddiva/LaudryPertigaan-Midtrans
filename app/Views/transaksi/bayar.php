<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-DiCeUhzEkKiExosF"></script>

<button id="pay-button">Bayar Sekarang</button>

<script>
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        snap.pay("<?= $snapToken ?>", {
            onSuccess: function (result) {
                alert("Pembayaran berhasil!");
                window.location.href = "<?= base_url('transaksi') ?>";
            },
            onPending: function (result) {
                alert("Menunggu pembayaran...");
                window.location.href = "<?= base_url('transaksi') ?>";
            },
            onError: function (result) {
                alert("Pembayaran gagal!");
            },
            onClose: function () {
                alert("Kamu belum menyelesaikan pembayaran.");
            }
        });
    });
</script>