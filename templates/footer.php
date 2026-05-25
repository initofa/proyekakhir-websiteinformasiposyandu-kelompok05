    </div> 
</div> 

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

function isMobile() {
    return window.innerWidth < 1024;
}

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


function openSidebarMobile() {
    if (!isMobile()) return;
    sidebar.classList.add('mobile-open');
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}


function closeSidebarMobile() {
    if (!isMobile()) return;
    sidebar.classList.remove('mobile-open');
    overlay.classList.add('hidden');
    document.body.style.overflow = '';
}


if (!isMobile()) {
    sidebar.addEventListener('mouseenter', () => {
        isHovering = true;
        openSidebarDesktop();
    });
    
    sidebar.addEventListener('mouseleave', () => {
        isHovering = false;
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


if (overlay) {
    overlay.addEventListener('click', () => {
        closeSidebarMobile();
    });
}


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


window.addEventListener('resize', () => {
    if (!isMobile()) {
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.add('hidden');
        document.body.style.overflow = '';
        
        closeSidebarDesktop();
        sidebar.style.width = '80px';
    } else {
        closeSidebarDesktop();
    }
});


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