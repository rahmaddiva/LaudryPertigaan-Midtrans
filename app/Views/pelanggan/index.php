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
                        Tambah Data Pelanggan
                    </button>
                    <br>
                    <div class="table-responsive mt-3">
                        <table class="datatables-basic table border-top">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pelanggan</th>
                                    <th>No Telepon</th>
                                    <th>Alamat</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($pelanggan as $a): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $a['nama_pelanggan'] ?></td>
                                        <td><?= $a['no_telepon'] ?></td>
                                        <td><?= $a['alamat'] ?></td>
                                        <td><?= $a['email'] ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                                    data-bs-target="#editModal<?= $a['id_pelanggan'] ?>">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <a href="/delete_pelanggan/<?= $a['id_pelanggan'] ?>"
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
                <h5 class="modal-title" id="exampleModalLabel1">Tambah Data Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/proses_pelanggan" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nama_pelanggan" class="form-label required">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                            value="<?= old('nama_pelanggan') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="no_telepon" class="form-label ">No Telepon</label>
                        <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                            value="<?= old('no_telepon') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label ">Alamat</label>
                        <input type="text" class="form-control" id="alamat" name="alamat" value="<?= old('alamat') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label ">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label ">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            value="<?= old('password') ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                    <!-- close -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- modal edit -->
<?php foreach ($pelanggan as $a): ?>
    <div class="modal fade" id="editModal<?= $a['id_pelanggan'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Pelanggan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/update_pelanggan/<?= $a['id_pelanggan'] ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_pelanggan" value="<?= $a['id_pelanggan'] ?>">
                        <div class="mb-3">
                            <label for="nama_pelanggan" class="form-label required">Nama Pelanggan</label>
                            <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan"
                                value="<?= esc($a['nama_pelanggan']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="no_telepon" class="form-label">No Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                                value="<?= esc($a['no_telepon']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <input type="text" class="form-control" id="alamat" name="alamat"
                                value="<?= esc($a['alamat']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= esc($a['email']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <small>(Kosongkan jika tidak
                                    diubah)</small></label>
                            <input type="password" class="form-control" id="password" name="password" value="">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php $this->endSection() ?>