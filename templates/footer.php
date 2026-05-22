</div>

<script>
// ============================================
// SIDEBAR FUNCTION
// ============================================
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const texts = document.querySelectorAll('.sidebar-text');
const menuItems = document.querySelectorAll('.menu-item');

let isOpen = false;

// ============================================
// OPEN SIDEBAR
// ============================================
function openSidebar() {
    if (isOpen) return;
    sidebar.classList.remove('w-20');
    sidebar.classList.add('w-64');
    mainContent.classList.remove('ml-24');
    mainContent.classList.add('ml-72');
    texts.forEach(el => {
        el.classList.remove('hidden');
    });
    isOpen = true;
}

// ============================================
// CLOSE SIDEBAR
// ============================================
function closeSidebar() {
    if (!isOpen) return;
    sidebar.classList.remove('w-64');
    sidebar.classList.add('w-20');
    mainContent.classList.remove('ml-72');
    mainContent.classList.add('ml-24');
    texts.forEach(el => {
        el.classList.add('hidden');
    });
    isOpen = false;
}

// ============================================
// HOVER SIDEBAR => OPEN
// ============================================
sidebar.addEventListener('mouseenter', () => {
    openSidebar();
});

// ============================================
// HOVER MAIN CONTENT => CLOSE
// ============================================
mainContent.addEventListener('mouseenter', () => {
    closeSidebar();
});

// ============================================
// CLICK MENU => CLOSE SIDEBAR
// ============================================
menuItems.forEach(item => {
    item.addEventListener('click', function() {
        setTimeout(() => {
            closeSidebar();
        }, 150);
    });
});

// ============================================
// SWEETALERT SUCCESS
// ============================================
<?php if (isset($_SESSION['success'])): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: '<?php echo addslashes($_SESSION['success']); ?>',
    confirmButtonColor: '#10b981',
    confirmButtonText: 'OK'
});
<?php unset($_SESSION['success']); endif; ?>

// ============================================
// SWEETALERT ERROR
// ============================================
<?php if (isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?php echo addslashes($_SESSION['error']); ?>',
    confirmButtonColor: '#dc2626',
    confirmButtonText: 'OK'
});
<?php unset($_SESSION['error']); endif; ?>

// ============================================
// SWEETALERT WARNING
// ============================================
<?php if (isset($_SESSION['warning'])): ?>
Swal.fire({
    icon: 'warning',
    title: 'Peringatan!',
    text: '<?php echo addslashes($_SESSION['warning']); ?>',
    confirmButtonColor: '#eab308',
    confirmButtonText: 'OK'
});
<?php unset($_SESSION['warning']); endif; ?>

// ============================================
// SWEETALERT INFO
// ============================================
<?php if (isset($_SESSION['info'])): ?>
Swal.fire({
    icon: 'info',
    title: 'Informasi',
    text: '<?php echo addslashes($_SESSION['info']); ?>',
    confirmButtonColor: '#3b82f6',
    confirmButtonText: 'OK'
});
<?php unset($_SESSION['info']); endif; ?>

// ============================================
// CONFIRM DELETE FUNCTION
// ============================================
function confirmDelete(event, url, message = 'Data akan dihapus permanen!') {
    event.preventDefault();
    Swal.fire({
        title: 'Yakin?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// ============================================
// CONFIRM ACTION FUNCTION (Terima/Tolak/Aktifkan)
// ============================================
function confirmAction(event, url, title, text, confirmText = 'Ya, lanjutkan!') {
    event.preventDefault();
    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#dc2626',
        confirmButtonText: confirmText,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// ============================================
// TOAST NOTIFICATION FUNCTION
// ============================================
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 z-50 px-6 py-3 rounded-xl shadow-lg flex items-center gap-3 transition-all duration-300 transform translate-x-0';
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    toast.className += ' ' + colors[type];
    toast.innerHTML = '<i class="fas ' + icons[type] + ' text-xl mr-2"></i>' + message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ============================================
// AUTO HIDE ALERT
// ============================================
document.querySelectorAll('.alert-auto').forEach(alert => {
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transform = 'translateX(100%)';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
});
</script>
</body>
</html>