<?php
/**
 * Template: Badge del Autor
 *
 * @package SaicoWC_Author_System
 * @version 1.0.0
 *
 * Variables disponibles:
 * @var array $badge_data Datos del badge
 * @var int $author_id ID del autor
 * @var int $size Tamaño del badge
 */

defined('ABSPATH') || exit;

if (!$badge_data) {
    return;
}

$gamification = saicowc_author_system()->gamification;
$level = $badge_data['level'];
$title = $badge_data['title'];
$points = $badge_data['points'];
$color = $badge_data['color'];

// Tooltip info
$tooltip = sprintf(
    /* translators: 1: título del nivel, 2: puntos */
    __('%1$s - %2$s puntos', 'saicowc-author'),
    $title,
    number_format($points)
);

if (isset($badge_data['next_level']) && $badge_data['next_level']) {
    $points_needed = $badge_data['next_level_points'] - $points;
    $tooltip .= ' | ' . sprintf(
        /* translators: %d: puntos restantes */
        __('%d puntos para el siguiente nivel', 'saicowc-author'),
        $points_needed
    );
}
?>

<span class="saicowc-author-badge" data-level="<?php echo esc_attr($level); ?>" title="<?php echo esc_attr($tooltip); ?>" style="--badge-color: <?php echo esc_attr($color); ?>;">
    <?php echo $gamification->get_badge_svg($level, $size); ?>
</span>
