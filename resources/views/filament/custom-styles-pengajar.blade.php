<style>
    /* Default (Light Mode) Colors */
    :root {
        /* Warna Biru Soft untuk Hover dan Aktif di Sidebar (Light Mode) */
        /* Berdasarkan warna tombol biru, kita cari versi soft-nya */
        --custom-sidebar-item-hover-bg: #E0F2FE; /* sky-100 atau light blue yang sangat soft */
        --custom-sidebar-item-active-bg: #E0F2FE; 

        /* Warna Teks dan Ikon untuk Hover dan Aktif (Light Mode) */
        /* Jika background soft, teks dan ikon perlu warna yang cukup kontras */
        --custom-sidebar-text-icon-hover-active: #0C4A6E; /* sky-800 atau biru gelap untuk kontras */

        /* Warna Border Kiri untuk Item Aktif (Light Mode) */
        --custom-sidebar-active-border-color: #60A5FA; /* Biru yang sedikit lebih kuat (misal: blue-400) */
    }

    /* Dark Mode Colors */
    html.dark {
        /* Warna Biru yang Tepat untuk Dark Mode (dari tombol 'Masuk & Kelola Pertemuan') */
        /* Berdasarkan screenshot tombol, warna birunya adalah #0EA5E9 (Sky-500) atau #0284C7 (Sky-600) */
        /* Mari kita coba #0EA5E9 (Sky-500) yang terlihat di screenshot */
        --custom-sidebar-item-hover-bg: #0EA5E9; 
        --custom-sidebar-item-active-bg: #0EA5E9; 

        /* Warna Teks dan Ikon untuk Hover dan Aktif (Dark Mode) */
        /* Karena background sudah biru solid, teks putih akan terlihat bagus */
        --custom-sidebar-text-icon-hover-active: #FFFFFF; 

        /* Warna Border Kiri untuk Item Aktif (Dark Mode) */
        --custom-sidebar-active-border-color: #0EA5E9; /* Bisa sama dengan background */
    }

    /* Common Styles (berlaku untuk Light dan Dark Mode) */
    /* Item Navigasi Sidebar - Hover State */
    .fi-sidebar-item > a:hover,
    .fi-sidebar-item > button:hover {
        background-color: var(--custom-sidebar-item-hover-bg) !important;
        border-radius: 8px; /* Menambahkan border-radius untuk sudut melengkung. Sesuaikan nilai ini. */
        padding-left: 16px; /* Contoh padding, sesuaikan dengan layout Anda */
        padding-right: 16px;
        transition: all 0.2s ease-in-out; /* Menambahkan transisi untuk efek yang lebih halus */
    }
    .fi-sidebar-item > a:hover .fi-sidebar-item-label,
    .fi-sidebar-item > button:hover .fi-sidebar-item-label,
    .fi-sidebar-item > a:hover .fi-sidebar-item-icon,
    .fi-sidebar-item > button:hover .fi-sidebar-item-icon {
        color: var(--custom-sidebar-text-icon-hover-active) !important;
    }

    /* Item Navigasi Sidebar - Active State */
    .fi-sidebar-item-active > a,
    .fi-sidebar-item-active > button {
        background-color: var(--custom-sidebar-item-active-bg) !important;
        border-radius: 8px; /* Juga terapkan pada status aktif */
        padding-left: 16px; /* Contoh padding, sesuaikan dengan layout Anda */
        padding-right: 16px;
        transition: all 0.2s ease-in-out;
    }
    .fi-sidebar-item-active .fi-sidebar-item-label,
    .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: var(--custom-sidebar-text-icon-hover-active) !important;
        font-weight: 600 !important;
    }
    
    /* Border kiri untuk item aktif (opsional) */
    .fi-sidebar .fi-sidebar-item.fi-sidebar-item-active > a::before,
    .fi-sidebar .fi-sidebar-item.fi-sidebar-item-active > button::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px; 
        background-color: var(--custom-sidebar-active-border-color); 
    }

</style>