<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="saico-header" id="saicoHeader">
    <div class="saico-header-container">

        <!-- Móvil: Hamburguesa (Izquierda) -->
        <button class="saico-menu-hamburguesa saico-solo-movil" id="saicoMenuToggle" aria-label="Menú">
            <span class="saico-hamburguesa-linea"></span>
            <span class="saico-hamburguesa-linea"></span>
            <span class="saico-hamburguesa-linea"></span>
        </button>

        <!-- Logo (Centrado en móvil, izquierda en desktop) -->
        <div class="saico-logo-contenedor">
            <a href="<?php echo home_url('/'); ?>" class="saico-logo">
                <?php
                $custom_logo_id = get_theme_mod('custom_logo');
                if ($custom_logo_id) {
                    $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                    echo '<img src="' . esc_url($logo_url) . '" alt="' . esc_attr(get_bloginfo('name')) . '" class="saico-logo-img">';
                } else {
                    echo '<span class="saico-logo-texto">' . get_bloginfo('name') . '</span>';
                }
                ?>
            </a>
        </div>

        <!-- Desktop: Búsqueda + Menú -->
        <div class="saico-search-menu-grupo saico-solo-escritorio">
            <!-- Búsqueda -->
            <div class="saico-search-contenedor">
                <input
                    type="text"
                    class="saico-search-input"
                    id="saicoSearchInput"
                    placeholder="<?php echo esc_attr(saico_get_search_placeholder()); ?>"
                    autocomplete="off"
                >
                <button class="saico-search-btn" id="saicoSearchBtn" aria-label="Buscar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
                <div class="saico-search-resultados" id="saicoSearchResults"></div>
            </div>

            <!-- Menú Hamburguesa Desktop -->
            <button class="saico-menu-hamburguesa" id="saicoMenuToggleDesktop" aria-label="Menú">
                <span class="saico-hamburguesa-linea"></span>
                <span class="saico-hamburguesa-linea"></span>
                <span class="saico-hamburguesa-linea"></span>
            </button>
        </div>

        <!-- Acciones del Header -->
        <div class="saico-header-acciones">

            <!-- CTA Button (Desktop) -->
            <?php if (saico_get_header_cta_show()) : ?>
            <a href="<?php echo esc_url(saico_get_header_cta_url()); ?>" class="saico-cta-btn saico-solo-escritorio">
                <?php echo esc_html(saico_get_header_cta_text()); ?>
            </a>
            <?php endif; ?>

            <!-- Carrito (Móvil + Desktop) -->
            <button class="saico-icono-btn saico-carrito-btn" id="saicoCarritoBtn" aria-label="Carrito">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <?php if (WC()->cart->get_cart_contents_count() > 0) : ?>
                <span class="saico-badge"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                <?php endif; ?>
            </button>

            <!-- Usuario (Solo Desktop) -->
            <?php if (is_user_logged_in()) :
                $current_user = wp_get_current_user();
                $avatar_url = get_avatar_url($current_user->ID, array('size' => 32));
            ?>
            <div class="saico-usuario-menu-wrapper saico-solo-escritorio">
                <button class="saico-icono-btn saico-usuario-btn" id="saicoUsuarioBtn" aria-label="Cuenta">
                    <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>" class="saico-usuario-avatar">
                </button>

                <!-- Dropdown Menú Usuario -->
                <div class="saico-usuario-dropdown" id="saicoUsuarioDropdown">
                    <div class="saico-usuario-dropdown-header">
                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>" class="dropdown-avatar">
                        <div class="dropdown-user-info">
                            <span class="dropdown-user-name"><?php echo esc_html($current_user->display_name); ?></span>
                            <span class="dropdown-user-email"><?php echo esc_html($current_user->user_email); ?></span>
                        </div>
                    </div>
                    <div class="saico-usuario-dropdown-menu">
                        <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="dropdown-menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Mi Cuenta</span>
                        </a>
                        <a href="<?php echo esc_url(wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'))); ?>" class="dropdown-menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span>Mis Pedidos</span>
                        </a>
                        <a href="<?php echo esc_url(wc_get_endpoint_url('downloads', '', wc_get_page_permalink('myaccount'))); ?>" class="dropdown-menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>Descargas</span>
                        </a>
                        <a href="<?php echo esc_url(wc_get_endpoint_url('edit-account', '', wc_get_page_permalink('myaccount'))); ?>" class="dropdown-menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 2v20M17 7H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                            <span>Configuración</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="dropdown-menu-item dropdown-logout">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>
            <?php else : ?>
            <button class="saico-icono-btn saico-usuario-btn saico-solo-escritorio" onclick="saicoAbrirModal('saico-login-modal')" aria-label="Iniciar Sesión">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </button>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Menú Lateral -->
<nav class="saico-menu-lateral" id="saicoMenuLateral">
    <div class="saico-menu-lateral-header">
        <h3>Menú</h3>
        <button class="saico-menu-cerrar" id="saicoMenuCerrar" aria-label="Cerrar">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Búsqueda en Menú Móvil -->
    <div class="saico-menu-busqueda saico-solo-movil">
        <input
            type="text"
            class="saico-search-input"
            id="saicoMenuSearchInput"
            placeholder="<?php echo esc_attr(saico_get_search_placeholder()); ?>"
            autocomplete="off"
        >
        <button class="saico-search-btn" aria-label="Buscar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.35-4.35"></path>
            </svg>
        </button>
        <div class="saico-search-resultados" id="saicoMenuSearchResults"></div>
    </div>

    <div class="saico-menu-contenido">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'movil',
            'menu_class' => 'saico-menu-lista',
            'container' => false,
            'fallback_cb' => false
        ));
        ?>
    </div>
</nav>

<!-- Overlay del Menú -->
<div class="saico-overlay" id="saicoOverlay"></div>

<!-- Bottom Navigation (Solo Móvil) -->
<nav class="saico-bottom-nav saico-solo-movil">
    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="saico-bottom-item <?php echo (is_shop() || is_product_category()) ? 'activo' : ''; ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <span>Explorar</span>
    </a>

    <button class="saico-bottom-item" id="saicoBottomBuscar">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"></path>
        </svg>
        <span>Buscar</span>
    </button>

    <?php if (is_user_logged_in()) : ?>
    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="saico-bottom-item <?php echo is_account_page() ? 'activo' : ''; ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span>Cuenta</span>
    </a>
    <?php else : ?>
    <button class="saico-bottom-item" onclick="saicoAbrirModal('saico-login-modal')">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span>Cuenta</span>
    </button>
    <?php endif; ?>

    <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="saico-bottom-item <?php echo (is_home() || is_single() || is_category()) ? 'activo' : ''; ?>">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        <span>Blog</span>
    </a>
</nav>

<?php
// Incluir modal de login si existe
if (file_exists(SAICO_DIR . '/partes/modal-login.php')) {
    get_template_part('partes/modal-login');
}
?>
