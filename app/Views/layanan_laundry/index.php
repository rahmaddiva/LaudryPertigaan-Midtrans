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
                        Tambah Data Layanan Laundry
                    </button>
                    <br>
                    <div class="table-responsive mt-3">
                        <table class="datatables-basic table border-top">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Layanan</th>
                                    <th>Harga Per Unit</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($layanan_laundry as $a): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $a['nama_layanan'] ?></td>
                                        <td><?= $a['harga_per_unit'] ?></td>
                                        <td><?= $a['satuan'] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?= $a['id_layanan'] ?>">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <a href="/delete_layanan_laundry/<?= $a['id_layanan'] ?>"
                                                    class="btn btn-outline-danger"
                                                    onclick="return confirm('Apakah Anda Yakin?')">
                                                    <i class="bx bx-trash"></i>
                                                </a>
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
                <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/proses_layanan_laundry" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nama_layanan" class="form-label required">Nama Layanan</label>
                        <input type="text" class="form-control" id="nama_layanan" name="nama_layanan"
                            value="<?= old('nama_layanan') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="harga_per_unit" class="form-label ">Harga Per Unit</label>
                        <input type="text" class="form-control" id="harga_per_unit" name="harga_per_unit"
                            value="<?= old('harga_per_unit') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="satuan_select" class="form-label">Satuan</label>
                        <select class="form-select" id="satuan_select" name="satuan_select"
                            onchange="toggleInputSatuan()">
                            <option value="">-- Pilih Satuan --</option>
                            <option value="kg" <?= old('satuan') == 'kg' ? 'selected' : '' ?>>kg</option>
                            <option value="m2" <?= old('satuan') == 'm2' ? 'selected' : '' ?>>m2</option>
                            <option value="pcs" <?= old('satuan') == 'pcs' ? 'selected' : '' ?>>pcs</option>
                            <option value="lainnya">Lainnya...</option>
                        </select>
                    </div>

                    <div class="mb-3" id="satuan_input_wrapper" style="display: none;">
                        <label for="satuan" class="form-label">Masukkan Satuan</label>
                        <input type="text" class="form-control" id="satuan" name="satuan" value="<?= old('satuan') ?>">
                    </div>

                    <script>
                        function toggleInputSatuan() {
                            const select = document.getElementById('satuan_select');
                            const inputWrapper = document.getElementById('satuan_input_wrapper');
                            const inputField = document.getElementById('satuan');

                            if (select.value === 'lainnya') {
                                inputWrapper.style.display = 'block';
                                inputField.value = '';
                            } else {
                                inputWrapper.style.display = 'none';
                                inputField.value = select.value;
                            }
                        }

                        // Panggil saat halaman dimuat, agar tampil input jika sebelumnya memilih "lainnya"
                        window.onload = function () {
                            toggleInputSatuan();
                        };
                    </script>


                    <button type="submit" class="btn btn-primary">Submit</button>
                    <!-- close -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal edit -->
<?php foreach ($layanan_laundry as $a): ?>
    <div class="modal fade" id="editModal<?= $a['id_layanan'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Layanan Laundry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/update_layanan_laundry/<?= $a['id_layanan'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_layanan" value="<?= $a['id_layanan'] ?>">
                        <div class="mb-3">
                            <label for="nama_layanan" class="form-label required">Nama Layanan</label>
                            <input type="text" class="form-control" id="nama_layanan" name="nama_layanan"
                                value="<?= esc($a['nama_layanan']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="harga_per_unit" class="form-label">Harga Per Unit</label>
                            <input type="text" class="form-control" id="harga_per_unit" name="harga_per_unit"
                                value="<?= esc($a['harga_per_unit']) ?>">
                        </div>
                        <?php
                        $satuan_opsi = ['kg', 'm2', 'pcs'];
                        $is_custom_satuan = !in_array($a['satuan'], $satuan_opsi);
                        ?>
                        <div class="mb-3">
                            <label for="satuan_select<?= $a['id_layanan'] ?>" class="form-label">Satuan</label>
                            <select class="form-select satuan-select" id="satuan_select<?= $a['id_layanan'] ?>"
                                name="satuan_select" onchange="toggleInputSatuan(<?= $a['id_layanan'] ?>)">
                                <option value="">-- Pilih Satuan --</option>
                                <option value="kg" <?= $a['satuan'] == 'kg' ? 'selected' : '' ?>>kg</option>
                                <option value="m2" <?= $a['satuan'] == 'm2' ? 'selected' : '' ?>>m2</option>
                                <option value="pcs" <?= $a['satuan'] == 'pcs' ? 'selected' : '' ?>>pcs</option>
                                <option value="lainnya" <?= $is_custom_satuan ? 'selected' : '' ?>>Lainnya...</option>
                            </select>
                        </div>

                        <div class="mb-3" id="satuan_input_wrapper<?= $a['id_layanan'] ?>"
                            style="display: <?= $is_custom_satuan ? 'block' : 'none' ?>;">
                            <label for="satuan<?= $a['id_layanan'] ?>" class="form-label">Masukkan Satuan</label>
                            <input type="text" class="form-control" id="satuan<?= $a['id_layanan'] ?>" name="satuan"
                                value="<?= $a['satuan'] ?>">
                        </div>

                        <script>
                            function toggleInputSatuan(id) {
                                const select = document.getElementById('satuan_select' + id);
                                const inputWrapper = document.getElementById('satuan_input_wrapper' + id);
                                const inputField = document.getElementById('satuan' + id);

                                if (select.value === 'lainnya') {
                                    inputWrapper.style.display = 'block';
                                    inputField.value = '';
                                } else {
                                    inputWrapper.style.display = 'none';
                                    inputField.value = select.value;
                                }
                            }

                            // Jalankan fungsi saat halaman dimuat
                            window.addEventListener('DOMContentLoaded', () => {
                                <?php foreach ($layanan_laundry as $a): ?>
                                    toggleInputSatuan(<?= $a['id_layanan'] ?>);
                                <?php endforeach; ?>
                            });
                        </script>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php $this->endSection() ?>