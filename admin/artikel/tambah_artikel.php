<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../auth/cek_admin.php';


$error_upload = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $id_kategori = $_POST['id_kategori'];
    $judul = $_POST['judul'];
    $konten = $_POST['konten'];
    $penulis_nik = $_SESSION['nik'];
    
    $thumbnail = '';
    
    function createSlug($text) {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');
        return $text;
    }
    
    if(isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name']){
        $file = $_FILES['thumbnail'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(!in_array($file_ext, $allowed_ext)){
            $error_upload = "Format gambar tidak valid! Gunakan: JPG, JPEG, PNG, GIF, atau WEBP.";
        }
        elseif($file_size > 5000000){
            $error_upload = "Ukuran gambar terlalu besar! Maksimal 5MB.";
        }
        elseif($file_error !== 0){
            $error_upload = "Terjadi kesalahan saat upload gambar!";
        }
        else {
            $slug = createSlug($judul);
            date_default_timezone_set('Asia/Jakarta');
            $timestamp = date('Ymd_His');
            $filename = $slug . '_' . $timestamp . '.' . $file_ext;
            
            if(strlen($filename) > 100) {
                $slug = substr($slug, 0, 50);
                $filename = $slug . '_' . $timestamp . '.' . $file_ext;
            }
            
            $upload_path = "../../uploads/artikel/";
            if(!file_exists($upload_path)){
                mkdir($upload_path, 0777, true);
            }
            
            $destination = $upload_path . $filename;
            
            if(move_uploaded_file($file_tmp, $destination)){
                $thumbnail = $filename;
            } else {
                $error_upload = "Gagal menyimpan gambar!";
            }
        }
    }
    
    if(empty($error_upload)){
        $query = "INSERT INTO artikel (id_kategori, judul, konten, thumbnail, penulis_nik) 
                  VALUES ('$id_kategori', '$judul', '$konten', '$thumbnail', '$penulis_nik')";
                  
        if(mysqli_query($conn, $query)){
            $_SESSION['success'] = "Artikel berhasil ditambahkan!";
            header("Location: list_artikel.php");
            exit();
        } else {
            $_SESSION['error'] = "Gagal menambahkan artikel: " . mysqli_error($conn);
            header("Location: list_artikel.php");
            exit();
        }
    } else {
        $_SESSION['error'] = $error_upload;
        header("Location: tambah_artikel.php");
        exit();
    }
}

$title = 'Tambah Artikel';
include __DIR__ . '/../../templates/sidebar.php';

$kategori = mysqli_query($conn, "SELECT * FROM kategori_artikel");
?>

<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-6 fade-in">
    <h1 class="text-2xl font-bold text-green-800 mb-6">Tambah Artikel</h1>
    
    <?php if(isset($_SESSION['error'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?php echo $_SESSION['error']; unset($_SESSION['error']); ?>',
        confirmButtonColor: '#dc2626'
    });
    </script>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" id="formArtikel">
        <div class="space-y-4">
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Kategori</label>
                <select name="id_kategori" required class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200">
                    <option disabled value="" selected>Pilih Kategori</option>
                    <?php while($k = mysqli_fetch_assoc($kategori)): ?>
                    <option value="<?php echo $k['id_kategori']; ?>"><?php echo htmlspecialchars($k['nama_kategori']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Judul Artikel</label>
                <input type="text" name="judul" id="judul" required 
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200"
                       placeholder="Masukkan judul artikel">
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Konten</label>
                <textarea name="konten" rows="10" required 
                          class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-200"
                          placeholder="Tulis konten artikel di sini..."></textarea>
            </div>
            
            <div>
                <label class="block font-semibold text-gray-700 mb-2">Thumbnail</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-green-400 transition cursor-pointer" id="dropzone">
                    <input type="file" name="thumbnail" id="thumbnail" accept="image/*" class="hidden">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">Klik atau drag & drop untuk upload gambar</p>
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG, GIF, WEBP | Max: 5MB</p>
                    <div id="preview" class="mt-3 hidden">
                        <img id="previewImg" src="#" alt="Preview" class="max-h-32 mx-auto rounded-lg shadow-sm">
                        <p id="previewName" class="text-sm text-gray-600 mt-1"></p>
                    </div>
                </div>
                <div id="fileError" class="text-red-500 text-sm mt-1 hidden"></div>
            </div>
        </div>
        
        <div class="flex gap-3 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-600 to-emerald-500 text-white py-2 rounded-xl font-semibold hover:shadow-lg transition">
                <i class="fas fa-save mr-2"></i> Simpan
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

dropzone.addEventListener('click', () => {
    fileInput.click();
});

dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.classList.add('border-green-500', 'bg-green-50');
});

dropzone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropzone.classList.remove('border-green-500', 'bg-green-50');
});

dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.classList.remove('border-green-500', 'bg-green-50');
    
    const files = e.dataTransfer.files;
    if(files.length > 0) {
        fileInput.files = files;
        validateAndPreview(files[0]);
    }
});

fileInput.addEventListener('change', function() {
    if(this.files.length > 0) {
        validateAndPreview(this.files[0]);
    }
});

function validateAndPreview(file) {
    const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if(!allowed.includes(file.type)) {
        fileError.classList.remove('hidden');
        fileError.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Format gambar tidak valid! Gunakan JPG, JPEG, PNG, GIF, atau WEBP.';
        preview.classList.add('hidden');
        fileInput.value = '';
        return;
    }
    
    if(file.size > 5000000) {
        fileError.classList.remove('hidden');
        fileError.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Ukuran gambar terlalu besar! Maksimal 5MB.';
        preview.classList.add('hidden');
        fileInput.value = '';
        return;
    }
    
    fileError.classList.add('hidden');
    
    const reader = new FileReader();
    reader.onload = function(e) {
        previewImg.src = e.target.result;
        previewName.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> ' + file.name;
        preview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}
</script>

<style>
#dropzone {
    transition: all 0.3s ease;
}
</style>

<?php include __DIR__ . '/../../templates/footer.php'; ?>