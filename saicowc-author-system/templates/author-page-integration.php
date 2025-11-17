<?php
/**
 * Template: Integración en Página de Autor
 *
 * @package SaicoWC_Author_System
 * @version 1.0.0
 *
 * Variables disponibles:
 * @var int $author_id ID del autor
 */

defined('ABSPATH') || exit;

$follow = saicowc_author_system()->follow;
$gamification = saicowc_author_system()->gamification;
$core = saicowc_author_system()->core;

$badge_data = $gamification->get_author_badge($author_id);
$followers_count = $follow->get_followers_count($author_id);
$points = $gamification->get_points($author_id);
$products_count = count_user_posts($author_id, 'product');
?>

<div class="saicowc-author-page-integration">
    <!-- Badge y Botón de Seguir -->
    <div class="author-header-actions">
        <?php if ($badge_data): ?>
        <div class="author-badge-display">
            <?php echo $core->get_author_badge_html($author_id, 48); ?>
            <div class="badge-info">
                <span class="badge-title"><?php echo esc_html($badge_data['title']); ?></span>
                <span class="badge-points"><?php echo esc_html(number_format($points)); ?> <?php esc_html_e('puntos', 'saicowc-author'); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php echo $core->get_follow_button_html($author_id); ?>
    </div>

    <!-- Estadísticas del Autor -->
    <div class="author-stats-panel">
        <div class="stat-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html(number_format($followers_count)); ?></span>
                <span class="stat-label"><?php esc_html_e('Seguidores', 'saicowc-author'); ?></span>
            </div>
        </div>

        <div class="stat-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html(number_format($products_count)); ?></span>
                <span class="stat-label"><?php esc_html_e('Productos', 'saicowc-author'); ?></span>
            </div>
        </div>

        <div class="stat-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
            <div class="stat-content">
                <span class="stat-value"><?php echo esc_html(number_format($points)); ?></span>
                <span class="stat-label"><?php esc_html_e('Puntos', 'saicowc-author'); ?></span>
            </div>
        </div>
    </div>

    <!-- Barra de Progreso -->
    <?php if ($badge_data && isset($badge_data['progress']) && $badge_data['progress'] < 100): ?>
    <div class="author-progress">
        <div class="progress-info">
            <span class="progress-label">
                <?php
                printf(
                    /* translators: 1: nivel actual, 2: siguiente nivel */
                    esc_html__('Progreso hacia %s', 'saicowc-author'),
                    esc_html($gamification->get_badge_levels()[$badge_data['next_level']]['title'])
                );
                ?>
            </span>
            <span class="progress-percent"><?php echo esc_html($badge_data['progress']); ?>%</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo esc_attr($badge_data['progress']); ?>%; background-color: <?php echo esc_attr($badge_data['color']); ?>;"></div>
        </div>
        <span class="progress-text">
            <?php
            printf(
                /* translators: %d: puntos restantes */
                esc_html__('%d puntos para el siguiente nivel', 'saicowc-author'),
                $badge_data['next_level_points'] - $points
            );
            ?>
        </span>
    </div>
    <?php endif; ?>
</div>
