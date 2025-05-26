<style>
    /* Default (Light Mode) Colors - Tetap seperti yang terakhir kita sepakati */
    :root {
        --custom-sidebar-item-hover-bg: #F0F8FF; /* AliceBlue, sangat lembut */
        --custom-sidebar-item-active-bg: #F0F8FF; 
        --custom-sidebar-text-icon-hover-active: #205072; /* Biru gelap yang lembut */
        --custom-sidebar-active-border-color: #90CAF9; /* Biru muda yang lembut */
    }

    /* Dark Mode Colors - Revisi untuk Biru yang Lebih Jauh Lebih Soft */
    html.dark {
        /* Warna Biru Super Soft untuk Dark Mode */
        /* Menggunakan warna yang sangat gelap, hampir abu-abu, dengan sedikit sentuhan biru */
        --custom-sidebar-item-hover-bg: #2C3E50; /* Contoh: Mirip 'Wet Asphalt' / Charcoal Blue, sangat gelap dan muted */
        --custom-sidebar-item-active-bg: #2C3E50; 

        /* Warna Teks dan Ikon untuk Hover dan Aktif (Dark Mode) */
        /* Teks putih atau abu-abu terang akan sangat cocok di atas background ini */
        --custom-sidebar-text-icon-hover-active: #ECF0F1; /* Mirip 'Clouds' / Light Gray */

        /* Warna Border Kiri untuk Item Aktif (Dark Mode) */
        --custom-sidebar-active-border-color: #3498DB; /* Biru sedang untuk border, cukup kontras tapi tidak terlalu cerah */
    }

    /* Common Styles (berlaku untuk Light dan Dark Mode) - Tidak Berubah */
    /* Item Navigasi Sidebar - Hover State */
    .fi-sidebar-item > a:hover,
    .fi-sidebar-item > button:hover {
        background-color: var(--custom-sidebar-item-hover-bg) !important;
        border-radius: 8px; 
        padding-left: 16px; 
        padding-right: 16px;
        transition: all 0.2s ease-in-out; 
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
        border-radius: 8px; 
        padding-left: 16px; 
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