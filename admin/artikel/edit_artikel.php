<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';

// ============================================
// PROSES FORM - HARUS SEBELUM SIDEBAR
// ============================================

$id = $_GET['id'];
$artikel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM artikel WHERE id_artikel=$id"));

if(!$artikel){
    $_SESSION['error'] = "Artikel tidak ditemukan!";
    header("Location: list_artikel.php");
    exit();
}

function createSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id_kategori = $_POST['id_kategori'];
    $judul = $_POST['judul'];
    $konten = $_POST['konten'];
    $updated_by = $_SESSION['nik'];
    
    $thumbnail_query = "";
    $error_upload = "";
    
    if($_FILES['thumbnail']['name']){
        $file = $_FILES['thumbnail'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(!in_array($file_ext, $allowed_ext)){
            $error_upload = "Format gambar tidak valid! Gunakan: JPG, JPEG, PNG, GIF, atau WEBP.";
        } elseif($file['size'] > 2000000){
            $error_upload = "Ukuran gambar terlalu besar! Maksimal 2MB.";
        } else {
            if($artikel['thumbnail'] && file_exists("../../uploads/artikel/" . $artikel['thumbnail'])){
                unlink("../../uploads/artikel/" . $artikel['thumbnail']);
            }
            
            $slug = createSlug($judul);
            date_default_timezone_set('Asia/Jakarta');
            $timestamp = date('Ymd_His');
            $filename = $slug . '_' . $timestamp . '.' . $file_ext;
            
            if(strlen($filename) > 100) {
                $slug = substr($slug, 0, 50);
                $filename = $slug . '_' . $timestamp . '.' . $file_ext;
            }
            
            if(move_uploaded_file($file['tmp_name'], "../../uploads/artikel/" . $filename)){
                $thumbnail_query = ", thumbnail='$filename'";
            } else {
                $error_upload = "Gagal menyimpan gambar!";
            }
        }
    }
    
    if(empty($error_upload)){
        $query = "UPDATE artikel SET id_kategori='$id_kategori', judul='$judul', konten='$konten', updated_by='$updated_by' $thumbnail_query WHERE id_artikel=$id";
        if(mysqli_query($conn, $query)){
            $_SESSION['success'] = "Artikel berhasil diupdate!";
            header("Location: list_artikel.php");
            exit();
        } else {
            $_SESSION['error'] = "Gagal mengupdate artikel!";
        }
    } else {
        $_SESSION['error'] = $error_upload;
    }
}

$title = 'Edit Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$kategori = mysqli_query($conn, "SELECT * FROM kategori_artikel");
?>

<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Edit Artikel</h1>
    
    <?php if(isset($_SESSION['error'])): ?>
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="space-y-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Kategori</label>
                <select name="id_kategori" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
                    <?php while($k = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?php echo $k['id_kategori']; ?>" <?php echo $k['id_kategori'] == $artikel['id_kategori'] ? 'selected' : ''; ?>>
                        <?php echo $k['nama_kategori']; ?>
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Judul Artikel</label>
                <input type="text" name="judul" id="judul" value="<?php echo htmlspecialchars($artikel['judul']); ?>" required 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400">
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Konten</label>
                <textarea name="konten" rows="10" required 
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400"><?php echo htmlspecialchars($artikel['konten']); ?></textarea>
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Ganti Thumbnail</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 transition cursor-pointer" id="dropzone">
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hidden">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">Klik atau drag & drop untuk upload gambar baru</p>
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG, GIF, WEBP | Max: 2MB</p>
                    <?php if($artikel['thumbnail']): ?>
                    <div id="oldThumbnail" class="mt-3">
                        <p class="text-sm text-gray-600">Thumbnail saat ini:</p>
                        <img src="../../uploads/artikel/<?php echo $artikel['thumbnail']; ?>" class="max-h-24 mx-auto mt-1 rounded-lg">
                        <p class="text-xs text-gray-400"><?php echo $artikel['thumbnail']; ?></p>
                    </div>
                    <?php endif; ?>
                    <div id="preview" class="mt-3 hidden">
                        <img id="previewImg" src="#" alt="Preview" class="max-h-32 mx-auto rounded-lg">
                        <p id="previewName" class="text-sm text-gray-600 mt-1"></p>
                    </div>
                </div>
                <div id="fileError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i> Update
            </button>
            <a href="list_artikel.php" class="flex-1 bg-gray-200 text-gray-700 text-center py-2 rounded-xl font-semibold hover:bg-gray-300 transition">
                <i class="fas fa-times mr-2"></i> Batal
            </a>
        </div>
    </form>
</div>

<script>
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('thumbnail');
const preview = document.getElementById('preview');
const previewImg = document.getElementById('previewImg');
const previewName = document.getElementById('previewName');
const fileError = document.getElementById('fileError');
const oldThumbnail = document.getElementById('oldThumbnail');

dropzone.addEventListener('click', () => { fileInput.click(); });
dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('border-green-500', 'bg-green-50'); });
dropzone.addEventListener('dragleave', (e) => { e.preventDefault(); dropzone.classList.remove('border-green-500', 'bg-green-50'); });
dropzone.addEventListener('drop', (e) => { e.preventDefault(); dropzone.classList.remove('border-green-500', 'bg-green-50'); if(e.dataTransfer.files.length > 0) { fileInput.files = e.dataTransfer.files; validateAndPreview(e.dataTransfer.files[0]); } });
fileInput.addEventListener('change', function() { if(this.files.length > 0) validateAndPreview(this.files[0]); });

function validateAndPreview(file) {
    const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if(!allowed.includes(file.type)) {
        fileError.classList.remove('hidden');
        fileError.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Format gambar tidak valid!';
        preview.classList.add('hidden');
        if(oldThumbnail) oldThumbnail.classList.remove('hidden');
        fileInput.value = '';
        return;
    }
    if(file.size > 2000000) {
        fileError.classList.remove('hidden');
        fileError.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Ukuran gambar terlalu besar! Maksimal 2MB.';
        preview.classList.add('hidden');
        if(oldThumbnail) oldThumbnail.classList.remove('hidden');
        fileInput.value = '';
        return;
    }
    fileError.classList.add('hidden');
    if(oldThumbnail) oldThumbnail.classList.add('hidden');
    const reader = new FileReader();
    reader.onload = function(e) { previewImg.src = e.target.result; previewName.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> ' + file.name; preview.classList.remove('hidden'); };
    reader.readAsDataURL(file);
}
</script>

<?php include __DIR__ . '/../../templates/footer.php'; ?>