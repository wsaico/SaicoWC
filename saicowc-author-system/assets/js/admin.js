/**
 * SaicoWC Author Follow & Badges - JavaScript Admin
 * Version: 1.0.0
 * Author: Wilber Saico
 */

(function($) {
    'use strict';

    /**
     * Clase Admin
     */
    class SaicoWCAuthorAdmin {
        constructor() {
            this.init();
        }

        /**
         * Inicializar
         */
        init() {
            this.initTabs();
            this.bindEvents();
        }

        /**
         * Inicializar tabs
         */
        initTabs() {
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                const target = $(this).attr('href');

                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                $('.settings-tab-content').removeClass('active').hide();
                $(target).addClass('active').fadeIn(300);
            });
        }

        /**
         * Bind de eventos
         */
        bindEvents() {
            // Confirmación al guardar
            $('form').on('submit', function() {
                // Aquí se puede añadir validación adicional
                return true;
            });
        }
    }

    /**
     * Inicializar al cargar el DOM
     */
    $(document).ready(function() {
        new SaicoWCAuthorAdmin();
    });

})(jQuery);
