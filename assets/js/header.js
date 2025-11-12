/**
 * Header JavaScript - Creado desde cero para SaicoWC
 * Maneja todas las interacciones del header
 */

(function($) {
    'use strict';

    // Variables globales
    let searchTimeout;
    const $body = $('body');
    const $header = $('#saicoHeader');
    const $menuLateral = $('#saicoMenuLateral');
    const $overlay = $('#saicoOverlay');

    /**
     * Inicializar cuando el DOM esté listo
     */
    $(document).ready(function() {
        initMenuToggle();
        initSearch();
        initBottomNav();
        initCarrito();
        initUsuario();
        initStickyHeader();
    });

    /**
     * Toggle del menú lateral
     */
    function initMenuToggle() {
        // Botón móvil
        $('#saicoMenuToggle').on('click', function(e) {
            e.preventDefault();
            toggleMenu();
        });

        // Botón desktop
        $('#saicoMenuToggleDesktop').on('click', function(e) {
            e.preventDefault();
            toggleMenu();
        });

        // Botón cerrar
        $('#saicoMenuCerrar').on('click', function(e) {
            e.preventDefault();
            closeMenu();
        });

        // Click en overlay
        $overlay.on('click', function() {
            closeMenu();
        });

        // ESC para cerrar
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMenu();
            }
        });
    }

    function toggleMenu() {
        if ($menuLateral.hasClass('activo')) {
            closeMenu();
        } else {
            openMenu();
        }
    }

    function openMenu() {
        $menuLateral.addClass('activo');
        $overlay.addClass('activo');
        $body.css('overflow', 'hidden');
        $('#saicoMenuToggle, #saicoMenuToggleDesktop').addClass('activo');
    }

    function closeMenu() {
        $menuLateral.removeClass('activo');
        $overlay.removeClass('activo');
        $body.css('overflow', '');
        $('#saicoMenuToggle, #saicoMenuToggleDesktop').removeClass('activo');
    }

    /**
     * Funcionalidad de búsqueda
     */
    function initSearch() {
        // Búsqueda desktop
        $('#saicoSearchInput').on('input', function() {
            const query = $(this).val().trim();
            handleSearch(query, '#saicoSearchResults');
        });

        $('#saicoSearchBtn').on('click', function() {
            const query = $('#saicoSearchInput').val().trim();
            if (query) {
                performSearch(query);
            }
        });

        // Búsqueda en menú móvil
        $('#saicoMenuSearchInput').on('input', function() {
            const query = $(this).val().trim();
            handleSearch(query, '#saicoMenuSearchResults');
        });

        // Enter para buscar
        $('#saicoSearchInput, #saicoMenuSearchInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                const query = $(this).val().trim();
                if (query) {
                    performSearch(query);
                }
            }
        });

        // Click fuera para cerrar resultados
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.saico-search-contenedor, .saico-menu-busqueda').length) {
                $('.saico-search-resultados').removeClass('activo');
            }
        });
    }

    function handleSearch(query, resultsSelector) {
        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $(resultsSelector).removeClass('activo').empty();
            return;
        }

        // Debounce para no hacer demasiadas peticiones
        searchTimeout = setTimeout(function() {
            fetchSearchResults(query, resultsSelector);
        }, 300);
    }

    function fetchSearchResults(query, resultsSelector) {
        const $results = $(resultsSelector);

        // Mostrar loading
        $results.addClass('activo').html('<div class="saico-search-loading">Buscando...</div>');

        $.ajax({
            url: saicoData.ajaxurl,
            type: 'POST',
            data: {
                action: 'saico_buscar_productos',
                nonce: saicoData.nonce,
                query: query
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $results.html(response.data.html);
                } else {
                    $results.html('<div class="saico-search-empty">No se encontraron resultados</div>');
                }
            },
            error: function() {
                $results.html('<div class="saico-search-error">Error al buscar</div>');
            }
        });
    }

    function performSearch(query) {
        // Ir a la página de búsqueda
        window.location.href = saicoData.shopUrl + '?s=' + encodeURIComponent(query);
    }

    /**
     * Bottom navigation (móvil)
     */
    function initBottomNav() {
        // Buscar desde bottom nav
        $('#saicoBottomBuscar').on('click', function() {
            openMenu();
            setTimeout(function() {
                $('#saicoMenuSearchInput').focus();
            }, 300);
        });

        // Cuenta desde bottom nav
        $('#saicoBottomCuenta').on('click', function() {
            // Si está logueado, ir a mi cuenta
            if (saicoData.isLoggedIn) {
                window.location.href = saicoData.myAccountUrl;
            } else {
                // Abrir modal de login
                if (typeof saicoAbrirModalLogin === 'function') {
                    saicoAbrirModalLogin('login');
                } else {
                    window.location.href = saicoData.myAccountUrl;
                }
            }
        });
    }

    /**
     * Carrito
     */
    function initCarrito() {
        $('#saicoCarritoBtn').on('click', function() {
            // Ir a la página del carrito
            window.location.href = saicoData.cartUrl;
        });
    }

    /**
     * Usuario
     */
    function initUsuario() {
        $('#saicoUsuarioBtn').on('click', function() {
            // Si está logueado, ir a mi cuenta
            if (saicoData.isLoggedIn) {
                window.location.href = saicoData.myAccountUrl;
            } else {
                // Abrir modal de login
                if (typeof saicoAbrirModalLogin === 'function') {
                    saicoAbrirModalLogin('login');
                } else {
                    window.location.href = saicoData.myAccountUrl;
                }
            }
        });
    }

    /**
     * Sticky header en scroll
     */
    function initStickyHeader() {
        let lastScroll = 0;
        const headerHeight = $header.outerHeight();

        $(window).on('scroll', function() {
            const currentScroll = $(this).scrollTop();

            // Añadir sombra al hacer scroll
            if (currentScroll > 10) {
                $header.addClass('scrolled');
            } else {
                $header.removeClass('scrolled');
            }

            lastScroll = currentScroll;
        });
    }

    /**
     * Actualizar contador del carrito via AJAX
     */
    function updateCartCount() {
        $.ajax({
            url: saicoData.ajaxurl,
            type: 'POST',
            data: {
                action: 'saico_get_cart_count',
                nonce: saicoData.nonce
            },
            success: function(response) {
                if (response.success) {
                    const count = response.data.count;
                    const $badge = $('.saico-badge');

                    if (count > 0) {
                        $badge.text(count).show();
                    } else {
                        $badge.hide();
                    }
                }
            }
        });
    }

    // Actualizar carrito cuando se agrega un producto
    $(document.body).on('added_to_cart', function() {
        updateCartCount();
    });

    // Exponer funciones globales si es necesario
    window.saicoHeader = {
        openMenu: openMenu,
        closeMenu: closeMenu,
        updateCartCount: updateCartCount
    };

})(jQuery);
