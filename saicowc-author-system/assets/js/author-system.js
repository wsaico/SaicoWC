/**
 * SaicoWC Author Follow & Badges - JavaScript Frontend
 * Version: 1.0.0
 * Author: Wilber Saico
 */

(function($) {
    'use strict';

    /**
     * Clase principal del plugin
     */
    class SaicoWCAuthorSystem {
        constructor() {
            this.init();
        }

        /**
         * Inicializar
         */
        init() {
            this.bindEvents();
            this.integrateWithTheme();
        }

        /**
         * Bind de eventos
         */
        bindEvents() {
            // Click en botón de seguir
            $(document).on('click', '.saicowc-follow-button', this.handleFollowClick.bind(this));

            // Hover en botón de seguir (cambiar texto)
            $(document).on('mouseenter', '.saicowc-follow-button.is-following', this.handleFollowHover.bind(this));
            $(document).on('mouseleave', '.saicowc-follow-button.is-following', this.handleFollowLeave.bind(this));
        }

        /**
         * Integración con theme SaicoWC
         * Inserta el botón de seguir y badge en las ubicaciones correctas
         */
        integrateWithTheme() {
            // Integración en single product
            this.integrateSingleProduct();

            // Integración en página de autor
            this.integrateAuthorPage();
        }

        /**
         * Integración en single product
         */
        integrateSingleProduct() {
            const $integrationData = $('#saicowc-author-integration-data');

            if ($integrationData.length === 0) {
                return;
            }

            const authorId = $integrationData.data('author-id');

            if (!authorId) {
                return;
            }

            // Buscar el elemento .stats-author-info
            const $statsAuthorInfo = $('.stats-author-info');

            if ($statsAuthorInfo.length === 0) {
                return;
            }

            // Obtener datos del autor via AJAX
            this.getAuthorData(authorId).then(data => {
                if (!data.success) {
                    return;
                }

                const authorData = data.data;

                // Crear badge HTML
                let badgeHTML = '';
                if (authorData.badge) {
                    badgeHTML = `<span class="saicowc-author-badge-inline" title="${authorData.badge.title}">${this.getBadgeSVG(authorData.badge.level, 24)}</span>`;
                }

                // Crear botón de seguir HTML
                const followButtonHTML = this.createFollowButtonHTML(authorId, authorData.is_following, authorData.followers_count);

                // Insertar badge junto al nombre del autor
                $statsAuthorInfo.find('.stats-author-name').after(badgeHTML);

                // Insertar botón de seguir
                $statsAuthorInfo.append(`<div class="author-follow-wrapper">${followButtonHTML}</div>`);
            });
        }

        /**
         * Integración en página de autor
         */
        integrateAuthorPage() {
            // Si existe el contenedor de integración, no hacer nada (ya se renderizó desde PHP)
            if ($('.saicowc-author-page-integration').length > 0) {
                return;
            }

            // Buscar la clase .autor-nombre
            const $autorNombre = $('.autor-nombre');

            if ($autorNombre.length === 0) {
                return;
            }

            // Obtener ID del autor de la URL
            const authorId = this.getAuthorIdFromUrl();

            if (!authorId) {
                return;
            }

            // Obtener datos del autor via AJAX
            this.getAuthorData(authorId).then(data => {
                if (!data.success) {
                    return;
                }

                const authorData = data.data;

                // Crear badge HTML
                if (authorData.badge) {
                    const badgeHTML = `<span class="saicowc-author-badge-inline" title="${authorData.badge.title}">${this.getBadgeSVG(authorData.badge.level, 32)}</span>`;
                    $autorNombre.after(badgeHTML);
                }

                // Crear botón de seguir HTML
                const followButtonHTML = this.createFollowButtonHTML(authorId, authorData.is_following, authorData.followers_count);

                // Insertar después del h1
                $autorNombre.after(`<div class="author-follow-wrapper" style="margin-top: 12px;">${followButtonHTML}</div>`);
            });
        }

        /**
         * Handle click en botón de seguir
         */
        handleFollowClick(e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const authorId = $button.data('author-id');
            const nonce = $button.data('nonce');
            const isFollowing = $button.hasClass('is-following');

            // Verificar login
            if (!saicowcAuthorData || typeof saicowcAuthorData === 'undefined') {
                alert('Error: Plugin no configurado correctamente');
                return;
            }

            // Estado loading
            $button.addClass('is-loading');
            const $text = $button.find('.follow-text');
            const originalText = $text.text();
            $text.text(saicowcAuthorData.i18n.loading);

            const action = isFollowing ? 'saicowc_unfollow_author' : 'saicowc_follow_author';

            // Petición AJAX
            $.ajax({
                url: saicowcAuthorData.ajax_url,
                type: 'POST',
                data: {
                    action: action,
                    author_id: authorId,
                    nonce: nonce
                },
                success: (response) => {
                    if (response.success) {
                        // Toggle estado
                        $button.toggleClass('is-following');

                        // Actualizar texto
                        const newText = response.data.is_following ? saicowcAuthorData.i18n.following : saicowcAuthorData.i18n.follow;
                        $text.text(newText);

                        // Actualizar contador
                        $button.find('.followers-count').text(this.formatNumber(response.data.followers_count));

                        // Animación
                        if (response.data.is_following) {
                            $button.addClass('just-followed');
                            setTimeout(() => {
                                $button.removeClass('just-followed');
                            }, 600);
                        }
                    } else {
                        alert(response.data.message || saicowcAuthorData.i18n.error);
                        $text.text(originalText);
                    }
                },
                error: () => {
                    alert(saicowcAuthorData.i18n.error);
                    $text.text(originalText);
                },
                complete: () => {
                    $button.removeClass('is-loading');
                }
            });
        }

        /**
         * Handle hover en botón de seguir
         */
        handleFollowHover(e) {
            const $button = $(e.currentTarget);
            const $text = $button.find('.follow-text');
            $text.text(saicowcAuthorData.i18n.unfollow);
        }

        /**
         * Handle leave en botón de seguir
         */
        handleFollowLeave(e) {
            const $button = $(e.currentTarget);
            const $text = $button.find('.follow-text');
            $text.text(saicowcAuthorData.i18n.following);
        }

        /**
         * Obtener datos del autor via AJAX
         */
        getAuthorData(authorId) {
            return $.ajax({
                url: saicowcAuthorData.ajax_url,
                type: 'GET',
                data: {
                    action: 'saicowc_get_author_stats',
                    author_id: authorId,
                    nonce: saicowcAuthorData.nonce
                }
            });
        }

        /**
         * Crear HTML del botón de seguir
         */
        createFollowButtonHTML(authorId, isFollowing, followersCount) {
            const followClass = isFollowing ? 'saicowc-follow-button is-following' : 'saicowc-follow-button';
            const followText = isFollowing ? saicowcAuthorData.i18n.following : saicowcAuthorData.i18n.follow;

            return `
                <button class="${followClass}"
                        data-author-id="${authorId}"
                        data-nonce="${saicowcAuthorData.nonce}">
                    <span class="follow-icon">
                        <svg class="icon-follow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <line x1="20" y1="8" x2="20" y2="14"></line>
                            <line x1="23" y1="11" x2="17" y2="11"></line>
                        </svg>
                        <svg class="icon-following" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <polyline points="17 11 19 13 23 9"></polyline>
                        </svg>
                    </span>
                    <span class="follow-text">${followText}</span>
                    <span class="followers-count">${this.formatNumber(followersCount)}</span>
                </button>
            `;
        }

        /**
         * Obtener SVG del badge
         */
        getBadgeSVG(level, size) {
            // Los SVG se renderizan desde PHP, aquí solo un placeholder
            return `<span class="badge-level-${level}" style="display:inline-block;width:${size}px;height:${size}px;">⭐</span>`;
        }

        /**
         * Obtener ID del autor desde la URL
         */
        getAuthorIdFromUrl() {
            const urlParams = new URLSearchParams(window.location.search);
            const authorParam = urlParams.get('author');

            if (authorParam) {
                return parseInt(authorParam);
            }

            // Intentar obtener del DOM
            const $body = $('body');
            const bodyClasses = $body.attr('class');

            if (bodyClasses) {
                const match = bodyClasses.match(/author-(\d+)/);
                if (match && match[1]) {
                    return parseInt(match[1]);
                }
            }

            return null;
        }

        /**
         * Formatear número
         */
        formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    }

    /**
     * Inicializar al cargar el DOM
     */
    $(document).ready(function() {
        new SaicoWCAuthorSystem();
    });

})(jQuery);
