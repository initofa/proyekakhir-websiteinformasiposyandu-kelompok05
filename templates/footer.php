<!-- templates/footer.php -->
    </div> <!-- Penutup mainContent -->
</div> <!-- Penutup desktop-wrapper -->

<script>
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const texts = document.querySelectorAll('.sidebar-text');
const menuItems = document.querySelectorAll('.menu-item');

const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const overlay = document.getElementById('overlay');
const logoutBtn = document.getElementById('logoutBtn');

let isOpen = false;
let isHovering = false;

// ============================
// CEK MOBILE
// ============================
function isMobile() {
    return window.innerWidth < 1024;
}

// ============================
// DESKTOP: OPEN SIDEBAR (expand)
// ============================
function openSidebarDesktop() {
    if (isMobile()) return;
    if (isOpen) return;
    
    sidebar.style.width = '280px';
    texts.forEach(el => {
        el.classList.remove('hidden');
        el.style.display = 'inline-block';
    });
    isOpen = true;
}

// ============================
// DESKTOP: CLOSE SIDEBAR (collapse)
// ============================
function closeSidebarDesktop() {
    if (isMobile()) return;
    if (!isOpen) return;
    
    sidebar.style.width = '80px';
    texts.forEach(el => {
        el.classList.add('hidden');
        el.style.display = 'none';
    });
    isOpen = false;
}

// ============================
// MOBILE: OPEN SIDEBAR
// ============================
function openSidebarMobile() {
    if (!isMobile()) return;
    sidebar.classList.add('mobile-open');
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// ============================
// MOBILE: CLOSE SIDEBAR
// ============================
function closeSidebarMobile() {
    if (!isMobile()) return;
    sidebar.classList.remove('mobile-open');
    overlay.classList.add('hidden');
    document.body.style.overflow = '';
}

// ============================
// DESKTOP HOVER EVENTS
// ============================
if (!isMobile()) {
    sidebar.addEventListener('mouseenter', () => {
        isHovering = true;
        openSidebarDesktop();
    });
    
    sidebar.addEventListener('mouseleave', () => {
        isHovering = false;
        // Delay close to prevent flicker
        setTimeout(() => {
            if (!isHovering && !isMobile()) {
                closeSidebarDesktop();
            }
        }, 100);
    });
    
    mainContent.addEventListener('mouseenter', () => {
        if (isOpen && !isHovering) {
            closeSidebarDesktop();
        }
    });
}

// ============================
// MOBILE BUTTON
// ============================
if (mobileMenuBtn) {
    mobileMenuBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (sidebar.classList.contains('mobile-open')) {
            closeSidebarMobile();
        } else {
            openSidebarMobile();
        }
    });
}

// ============================
// OVERLAY CLICK (mobile)
// ============================
if (overlay) {
    overlay.addEventListener('click', () => {
        closeSidebarMobile();
    });
}

// ============================
// LOGOUT CONFIRMATION
// ============================
if (logoutBtn) {
    logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: 'Anda akan keluar dari sistem dan perlu login kembali',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= $base_url ?>/auth/logout.php';
            }
        });
    });
}

// ============================
// MENU CLICK - tutup sidebar di mobile
// ============================
menuItems.forEach(item => {
    item.addEventListener('click', function() {
        if (this.id === 'logoutBtn') return;
        
        if (isMobile()) {
            setTimeout(() => {
                closeSidebarMobile();
            }, 200);
        }
    });
});

// ============================
// WINDOW RESIZE
// ============================
window.addEventListener('resize', () => {
    if (!isMobile()) {
        // Reset mobile state
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.add('hidden');
        document.body.style.overflow = '';
        
        // Reset desktop sidebar to mini
        closeSidebarDesktop();
        sidebar.style.width = '80px';
    } else {
        // Reset desktop state
        closeSidebarDesktop();
    }
});

// ============================================
// SWEETALERT NOTIFICATIONS
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

<?php if (isset($_SESSION['error'])): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal!',
    text: '<?php echo addslashes($_SESSION['error']); ?>',
    confirmButtonColor: '#dc2626',
    confirmButtonText: 'OK'
});
<?php unset($_SESSION['error']); endif; ?>

<?php if (isset($_SESSION['warning'])): ?>
Swal.fire({
    icon: 'warning',
    title: 'Peringatan!',
    text: '<?php echo addslashes($_SESSION['warning']); ?>',
    confirmButtonColor: '#eab308',
    confirmButtonText: 'OK'
});
<?php unset($_SESSION['warning']); endif; ?>

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
// CONFIRM DELETE
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
</script>
</body>
</html>