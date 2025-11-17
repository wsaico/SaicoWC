<?php
/**
 * Template: Botón de Seguir/Dejar de Seguir
 *
 * @package SaicoWC_Author_System
 * @version 1.0.0
 *
 * Variables disponibles:
 * @var int $author_id ID del autor
 */

defined('ABSPATH') || exit;

$user_id = get_current_user_id();
$follow = saicowc_author_system()->follow;

$is_following = false;
$followers_count = $follow->get_followers_count($author_id);

if ($user_id) {
    $is_following = $follow->is_following($user_id, $author_id);

    // No mostrar si es el propio usuario
    if ($user_id == $author_id) {
        return;
    }
}

$button_class = 'saicowc-follow-button';
if ($is_following) {
    $button_class .= ' is-following';
}
?>

<button
    class="<?php echo esc_attr($button_class); ?>"
    data-author-id="<?php echo esc_attr($author_id); ?>"
    data-nonce="<?php echo esc_attr(wp_create_nonce('saicowc_author_nonce')); ?>"
    aria-label="<?php echo $is_following ? esc_attr__('Dejar de seguir', 'saicowc-author') : esc_attr__('Seguir', 'saicowc-author'); ?>"
>
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
    <span class="follow-text" data-follow="<?php esc_attr_e('Seguir', 'saicowc-author'); ?>" data-following="<?php esc_attr_e('Siguiendo', 'saicowc-author'); ?>" data-unfollow="<?php esc_attr_e('Dejar de seguir', 'saicowc-author'); ?>">
        <?php echo $is_following ? esc_html__('Siguiendo', 'saicowc-author') : esc_html__('Seguir', 'saicowc-author'); ?>
    </span>
    <span class="followers-count">
        <?php echo esc_html(number_format($followers_count)); ?>
    </span>
</button>
