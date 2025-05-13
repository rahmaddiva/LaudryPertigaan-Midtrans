<?php $this->extend('templates/main') ?>
<?php $this->section('content') ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <?php if (session()->getFlashdata('validation')): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach (session()->getFlashdata('validation')->getErrors() as $name => $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <!-- success -->
                <script>
                    // toast error message
                    <?php if (session()->getFlashdata('error')): ?>
                        $(document).ready(function () {
                            toastr.error('<?= session()->getFlashdata('error') ?>');
                        });
                    <?php endif; ?>
                    // toast success message
                    <?php if (session()->getFlashdata('success')): ?>
                        $(document).ready(function () {
                            toastr.success('<?= session()->getFlashdata('success') ?>');
                        });
                    <?php endif; ?>
                </script>
                <div class="card-body col-lg-12">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                        data-bs-target="#basicModal">
                        Tambah Data Transaksi
                    </button>
                    <br>
                    <div class="table-responsive mt-3">
                        <table class="datatables-basic table border-top">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Kode Transaksi</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Status</th>
                                    <th>Total Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($transaksi as $a): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $a['nama_pelanggan'] ?></td>
                                        <td><?= $a['kode_transaksi'] ?></td>
                                        <td><?= $a['tanggal_masuk'] ?></td>
                                        <td>
                                            <?php if ($a['status'] == 'Berhasil'): ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Proses</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $a['total_harga'] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?= $a['id_transaksi'] ?>">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <a href="/delete_transaksi/<?= $a['id_transaksi'] ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('Apakah Anda Yakin?')">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                                <!-- Tombol Bayar -->
                                                <a href="#" class="btn btn-outline-success" data-bs-toggle="modal"
                                                    data-bs-target="#modalBayar<?= $a['id_transaksi'] ?>">
                                                    <i class="bx bx-money"></i> Bayar
                                                </a>

                                                <!-- Modal Bayar -->
                                                <div class="modal fade" id="modalBayar<?= $a['id_transaksi'] ?>"
                                                    tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <form id="form-bayar-<?= $a['id_transaksi'] ?>">

                                                                <input type="hidden" name="id_transaksi"
                                                                    value="<?= $a['id_transaksi'] ?>">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Pembayaran Transaksi
                                                                        #<?= $a['id_transaksi'] ?></h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="metode" class="form-label">Metode
                                                                            Pembayaran</label>
                                                                        <select name="metode_pembayaran"
                                                                            class="form-select metode-select"
                                                                            data-id="<?= $a['id_transaksi'] ?>" required>
                                                                            <option value="">-- Pilih --</option>
                                                                            <option value="midtrans">Midtrans</option>
                                                                            <option value="cash">Cash</option>
                                                                        </select>
                                                                    </div>
                                                                    <div id="midtrans-button-<?= $a['id_transaksi'] ?>"
                                                                        style="display: none;">
                                                                        <button type="button"
                                                                            id="pay-button-<?= $a['id_transaksi'] ?>"
                                                                            class="btn btn-success">Bayar Sekarang</button>
                                                                    </div>
                                                                    <p class="mt-3">Total Bayar: <strong>Rp
                                                                            <?= number_format($a['total_harga'], 0, ',', '.') ?></strong>
                                                                    </p>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <script src="https://app.sandbox.midtrans.com/snap/snap.js"
                                                    data-client-key="SB-Mid-client-DiCeUhzEkKiExosF"></script>
                                                <script>
                                                    document.querySelectorAll('.metode-select').forEach(function (select) {
                                                        select.addEventListener('change', function () {
                                                            const id = this.dataset.id;
                                                            const metode = this.value;
                                                            const btnContainer = document.getElementById('midtrans-button-' + id);

                                                            if (metode === 'midtrans') {
                                                                btnContainer.style.display = 'block';

                                                                document.getElementById('pay-button-' + id).onclick = function () {
                                                                    fetch('/transaksi/bayar/' + id)
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            if (data.status === 'success') {
                                                                                snap.pay(data.snapToken, {
                                                                                    onSuccess: function () {
                                                                                        alert("Pembayaran berhasil!");
                                                                                        window.location.reload();
                                                                                    },
                                                                                    onPending: function () {
                                                                                        alert("Menunggu pembayaran...");
                                                                                        window.location.reload();
                                                                                    },
                                                                                    onError: function () {
                                                                                        alert("Pembayaran gagal!");
                                                                                    },
                                                                                    onClose: function () {
                                                                                        alert("Kamu belum menyelesaikan pembayaran.");
                                                                                    }
                                                                                });
                                                                            } else {
                                                                                alert("Gagal mendapatkan Snap Token: " + data.message);
                                                                            }
                                                                        });
                                                                };
                                                            } else {
                                                                btnContainer.style.display = 'none';
                                                            }
                                                        });
                                                    });

                                                </script>

                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/proses_transaksi" method="post">
                    <?= csrf_field() ?>
                    <!-- Pilih pelanggan -->
                    <div class="mb-3">
                        <label for="pelanggan" class="form-label">Pilih Pelanggan</label>
                        <select name="id_pelanggan" class="form-select" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            <?php foreach ($pelanggan as $p): ?>
                                <option value="<?= $p['id_pelanggan'] ?>"><?= $p['nama_pelanggan'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tanggal transaksi -->
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="<?= date('Y-m-d') ?>"
                            required>
                    </div>

                    <!-- Detail layanan -->
                    <div id="layanan-wrapper">
                        <div class="row mb-3 layanan-item">
                            <div class="col-md-4">
                                <label>Layanan</label>
                                <select class="form-select layanan-select" name="layanan[]"
                                    onchange="updateHarga(this)">
                                    <option value="" data-harga="0">-- Pilih Layanan --</option>
                                    <?php foreach ($layanan as $l): ?>
                                        <option value="<?= $l['id_layanan'] ?>" data-harga="<?= $l['harga_per_unit'] ?>">
                                            <?= $l['nama_layanan'] ?> (<?= $l['harga_per_unit'] ?>/<?= $l['satuan'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label>Jumlah</label>
                                <input type="number" name="jumlah[]" class="form-control" oninput="updateTotal(this)"
                                    min="1" value="1">
                            </div>
                            <div class="col-md-3">
                                <label>Total Harga</label>
                                <input type="text" class="form-control total-harga" readonly>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" onclick="hapusLayanan(this)">Hapus</button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary mb-3" onclick="tambahLayanan()">+ Tambah
                        Layanan</button>

                    <!-- Total keseluruhan -->
                    <div class="mb-3">
                        <label>Total Keseluruhan</label>
                        <input type="text" class="form-control" id="grand-total" readonly>
                    </div>
                    <script>
                        function updateHarga(selectElem) {
                            const row = selectElem.closest('.layanan-item');
                            const harga = parseInt(selectElem.options[selectElem.selectedIndex].getAttribute('data-harga')) || 0;
                            const jumlah = parseInt(row.querySelector('[name="jumlah[]"]').value) || 0;
                            const total = harga * jumlah;
                            row.querySelector('.total-harga').value = total;
                            updateGrandTotal();
                        }

                        function updateTotal(inputElem) {
                            const row = inputElem.closest('.layanan-item');
                            const select = row.querySelector('.layanan-select');
                            updateHarga(select);
                        }

                        function updateGrandTotal() {
                            let total = 0;
                            document.querySelectorAll('.total-harga').forEach(el => {
                                total += parseInt(el.value) || 0;
                            });
                            document.getElementById('grand-total').value = total;
                        }

                        function tambahLayanan() {
                            const wrapper = document.getElementById('layanan-wrapper');
                            const item = wrapper.querySelector('.layanan-item');
                            const clone = item.cloneNode(true);

                            // reset field
                            clone.querySelector('.layanan-select').value = '';
                            clone.querySelector('[name="jumlah[]"]').value = 1;
                            clone.querySelector('.total-harga').value = '';

                            wrapper.appendChild(clone);
                        }

                        function hapusLayanan(btn) {
                            const wrapper = document.getElementById('layanan-wrapper');
                            const item = btn.closest('.layanan-item');
                            if (wrapper.querySelectorAll('.layanan-item').length > 1) {
                                item.remove();
                                updateGrandTotal();
                            }
                        }
                    </script>
                    <input type="hidden" name="total_harga">
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal edit -->
<?php foreach ($transaksi as $a): ?>
    <div class="modal fade" id="editModal<?= $a['id_transaksi'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('update_transaksi/' . $a['id_transaksi']) ?>" method="post">
                        <?= csrf_field() ?>
                        <!-- Pelanggan -->
                        <div class="mb-3">
                            <label for="id_pelanggan" class="form-label">Pelanggan</label>

                            <select name="id_pelanggan" id="id_pelanggan" class="form-select" required>
                                <option value="">-- Pilih Pelanggan --</option>
                                <?php foreach ($pelanggan as $p): ?>
                                    <option value="<?= $p['id_pelanggan'] ?>" <?= $p['id_pelanggan'] == $a['id_pelanggan'] ? 'selected' : '' ?>>
                                        <?= $p['nama_pelanggan'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="mb-3">
                            <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control"
                                value="<?= old('tanggal_masuk', $a['tanggal_masuk']) ?>" required>
                        </div>
                        <!-- Layanan -->
                        <hr>
                        <h5>Detail Layanan</h5>
                        <div id="layanan_wrapper">
                            <?php foreach ($detail as $index => $d): ?>
                                <div class="row mb-2 layanan-item">
                                    <div class="col-md-6">
                                        <select name="layanan[]" class="form-select" required>
                                            <option value="">-- Pilih Layanan --</option>
                                            <?php foreach ($layanan as $l): ?>
                                                <option value="<?= $l['id_layanan'] ?>" <?= $l['id_layanan'] == $d['id_layanan'] ? 'selected' : '' ?>>
                                                    <?= $l['nama_layanan'] ?> (<?= $l['harga_per_unit'] ?>/<?= $l['satuan'] ?>)
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" name="jumlah[]" class="form-control" min="1"
                                            value="<?= $d['jumlah'] ?>" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm"
                                            onclick="hapusLayanan1(this)">Hapus</button>
                                    </div>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <button type="button" class="btn btn-secondary btn-sm mb-3" onclick="tambahLayanan1()">+ Tambah
                            Layanan</button>
                        <div class="mb-3">
                            <label for="total_harga" class="form-label">Total Harga</label>
                            <input type="number" name="total_harga" id="total_harga" class="form-control"
                                value="<?= $a['total_harga'] ?>" readonly>
                        </div>


                        <!-- Submit -->
                        <button type="submit" class="btn btn-primary">Update Transaksi</button>
                    </form>
                </div>
                <!-- Template Layanan (disembunyikan) -->
                <div id="template_layanan" style="display: none;">
                    <div class="row mb-2 layanan-item">
                        <div class="col-md-6">
                            <select name="layanan[]" class="form-select layanan-select" onchange="hitungTotal1()" required>
                                <option value="">-- Pilih Layanan --</option>
                                <?php foreach ($layanan as $l): ?>
                                    <option value="<?= $l['id_layanan'] ?>" data-harga="<?= $l['harga_per_unit'] ?>">
                                        <?= $l['nama_layanan'] ?> (<?= $l['harga_per_unit'] ?>/<?= $l['satuan'] ?>)
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="jumlah[]" class="form-control jumlah-input" min="1" required
                                oninput="hitungTotal1()">

                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-sm" onclick="hapusLayanan1(this)">Hapus</button>
                        </div>
                    </div>
                </div>

                <script>
                    function tambahLayanan1() {
                        const wrapper = document.getElementById('layanan_wrapper');
                        const template = document.getElementById('template_layanan').innerHTML;
                        wrapper.insertAdjacentHTML('beforeend', template);
                        hitungTotal(); // panggil ulang untuk hitung ulang total
                    }

                    function hapusLayanan1(btn) {
                        btn.closest('.layanan-item').remove();
                        hitungTotal(); // update total setelah hapus
                    }

                </script>
                <script>
                    function hitungTotal1() {
                        let total = 0;
                        const layananItems = document.querySelectorAll('.layanan-item');

                        layananItems.forEach(function (item) {
                            const select = item.querySelector('.layanan-select');
                            const jumlahInput = item.querySelector('.jumlah-input');
                            if (!select || !jumlahInput) return;

                            const harga = parseInt(select.selectedOptions[0]?.dataset.harga || 0);
                            const jumlah = parseInt(jumlahInput.value || 0);

                            total += harga * jumlah;
                        });

                        document.getElementById('total_harga').value = total;
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        hitungTotal();
                    });
                </script>

            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php $this->endSection() ?>