<?php
/**
 * Template: Product Reviews (Reseñas de Producto)
 * Versión WooCommerce personalizada con sistema de estrellas
 *
 * @package SaicoWC
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (!comments_open()) {
    return;
}
?>

<div id="reviews" class="saico-product-reviews">
    <div id="comments">

        <?php if (have_comments()) : ?>
            <!-- Resumen de Reviews -->
            <div class="reviews-summary">
                <div class="rating-overview">
                    <div class="average-rating">
                        <span class="rating-number"><?php echo number_format($product->get_average_rating(), 1); ?></span>
                        <div class="rating-stars">
                            <?php echo saico_get_star_rating_html($product->get_average_rating()); ?>
                        </div>
                        <span class="rating-count"><?php echo $product->get_review_count(); ?> reseñas</span>
                    </div>
                </div>
            </div>

            <!-- Lista de Reviews -->
            <ol class="reviews-list">
                <?php wp_list_comments(apply_filters('woocommerce_product_review_list_args', array(
                    'callback' => 'saico_product_review_template',
                ))); ?>
            </ol>

            <?php
            if (get_comment_pages_count() > 1 && get_option('page_comments')) :
                echo '<nav class="reviews-pagination">';
                paginate_comments_links(apply_filters('woocommerce_comment_pagination_args', array(
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                    'type'      => 'list',
                )));
                echo '</nav>';
            endif;
            ?>

        <?php else : ?>
            <p class="no-reviews">Aún no hay reseñas.</p>
        <?php endif; ?>
    </div>

    <?php if (get_option('woocommerce_review_rating_verification_required') === 'no' || wc_customer_bought_product('', get_current_user_id(), $product->get_id())) : ?>

        <div id="review_form_wrapper">
            <div id="review_form">
                <?php
                $commenter    = wp_get_current_commenter();
                $comment_form = array(
                    'title_reply'          => have_comments() ? __('Agregar una reseña', 'woocommerce') : sprintf(__('Sé el primero en opinar sobre "%s"', 'woocommerce'), get_the_title()),
                    'title_reply_to'       => __('Responder a %s', 'woocommerce'),
                    'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
                    'title_reply_after'    => '</h3>',
                    'comment_notes_after'  => '',
                    'label_submit'         => __('Enviar', 'woocommerce'),
                    'logged_in_as'         => '',
                    'comment_field'        => '',
                );

                $name_email_required = (bool) get_option('require_name_email', 1);
                $fields              = array(
                    'author' => array(
                        'label'    => __('Nombre', 'woocommerce'),
                        'type'     => 'text',
                        'value'    => $commenter['comment_author'],
                        'required' => $name_email_required,
                    ),
                    'email'  => array(
                        'label'    => __('Email', 'woocommerce'),
                        'type'     => 'email',
                        'value'    => $commenter['comment_author_email'],
                        'required' => $name_email_required,
                    ),
                );

                $comment_form['fields'] = array();

                foreach ($fields as $key => $field) {
                    $field_html  = '<p class="comment-form-' . esc_attr($key) . '">';
                    $field_html .= '<label for="' . esc_attr($key) . '">' . esc_html($field['label']);

                    if ($field['required']) {
                        $field_html .= '&nbsp;<span class="required">*</span>';
                    }

                    $field_html .= '</label><input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($field['type']) . '" value="' . esc_attr($field['value']) . '" size="30" ' . ($field['required'] ? 'required' : '') . ' /></p>';

                    $comment_form['fields'][$key] = $field_html;
                }

                $account_page_url = wc_get_page_permalink('myaccount');
                if ($account_page_url) {
                    $comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf(__('Debes <a href="%s">iniciar sesión</a> para publicar una reseña.', 'woocommerce'), esc_url($account_page_url)) . '</p>';
                }

                // Sistema de estrellas
                if (wc_review_ratings_enabled()) {
                    $comment_form['comment_field'] = '<div class="comment-form-rating">
                        <label for="rating">' . esc_html__('Tu calificación', 'woocommerce') . '&nbsp;<span class="required">*</span></label>
                        <div class="stars-input" id="starsInput">
                            <span class="star" data-rating="1">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </span>
                            <span class="star" data-rating="2">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </span>
                            <span class="star" data-rating="3">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </span>
                            <span class="star" data-rating="4">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </span>
                            <span class="star" data-rating="5">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                            </span>
                        </div>
                        <select name="rating" id="rating" required style="display:none;">
                            <option value="">' . esc_html__('Rate&hellip;', 'woocommerce') . '</option>
                            <option value="5">' . esc_html__('Perfect', 'woocommerce') . '</option>
                            <option value="4">' . esc_html__('Good', 'woocommerce') . '</option>
                            <option value="3">' . esc_html__('Average', 'woocommerce') . '</option>
                            <option value="2">' . esc_html__('Not that bad', 'woocommerce') . '</option>
                            <option value="1">' . esc_html__('Very poor', 'woocommerce') . '</option>
                        </select>
                    </div>';
                }

                $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Tu reseña', 'woocommerce') . '&nbsp;<span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

                comment_form(apply_filters('woocommerce_product_review_comment_form_args', $comment_form));
                ?>
            </div>
        </div>

    <?php else : ?>
        <p class="review-form-login"><?php esc_html_e('Sólo los usuarios registrados que hayan comprado este producto pueden escribir una reseña.', 'woocommerce'); ?></p>
    <?php endif; ?>

    <div class="clear"></div>
</div>

<?php
/**
 * Callback para mostrar cada review individual
 */
function saico_product_review_template($comment, $args, $depth) {
    ?>
    <li <?php comment_class('review-item'); ?> id="li-comment-<?php comment_ID(); ?>">
        <div id="comment-<?php comment_ID(); ?>" class="review-body">
            <div class="review-avatar">
                <?php echo get_avatar($comment, 60); ?>
            </div>

            <div class="review-content">
                <div class="review-header">
                    <div class="review-author-info">
                        <strong class="review-author"><?php comment_author(); ?></strong>
                        <?php
                        if ('1' === get_comment_meta($comment->comment_ID, 'verified', true)) {
                            echo '<span class="verified-badge" title="Compra verificada">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                </svg>
                                Verificado
                            </span>';
                        }
                        ?>
                    </div>

                    <div class="review-rating">
                        <?php
                        $rating = intval(get_comment_meta($comment->comment_ID, 'rating', true));
                        if ($rating && wc_review_ratings_enabled()) {
                            echo saico_get_star_rating_html($rating);
                        }
                        ?>
                    </div>
                </div>

                <time class="review-date" datetime="<?php echo get_comment_date('c'); ?>">
                    <?php echo get_comment_date('d/m/Y'); ?>
                </time>

                <div class="review-text">
                    <?php comment_text(); ?>
                </div>
            </div>
        </div>
    </li>
    <?php
}

/**
 * Helper: Generar HTML de estrellas
 */
function saico_get_star_rating_html($rating) {
    $rating = (float) $rating;
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

    $html = '<div class="star-rating">';

    // Estrellas llenas
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '<svg class="star star-full" viewBox="0 0 24 24" fill="currentColor" stroke="none">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
        </svg>';
    }

    // Media estrella
    if ($half_star) {
        $html .= '<svg class="star star-half" viewBox="0 0 24 24" fill="currentColor" stroke="none">
            <defs>
                <linearGradient id="half-fill">
                    <stop offset="50%" stop-color="currentColor"/>
                    <stop offset="50%" stop-color="transparent"/>
                </linearGradient>
            </defs>
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="url(#half-fill)"></path>
        </svg>';
    }

    // Estrellas vacías
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '<svg class="star star-empty" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
        </svg>';
    }

    $html .= '</div>';

    return $html;
}
?>

<style>
/* ============================================
   REVIEWS CONTAINER
   ============================================ */
.saico-product-reviews {
    width: 100%;
}

/* ============================================
   RESUMEN DE REVIEWS
   ============================================ */
.reviews-summary {
    padding: var(--saico-spacing-2xl);
    background: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-lg);
    margin-bottom: var(--saico-spacing-2xl);
}

.rating-overview {
    text-align: center;
}

.average-rating {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--saico-spacing-sm);
}

.rating-number {
    font-size: 48px;
    font-weight: var(--saico-font-weight-bold);
    color: var(--saico-texto-primario);
    line-height: 1;
}

.rating-stars {
    display: flex;
    gap: 4px;
}

.rating-count {
    font-size: var(--saico-font-sm);
    color: var(--saico-texto-terciario);
}

/* ============================================
   LISTA DE REVIEWS
   ============================================ */
.reviews-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.review-item {
    margin-bottom: var(--saico-spacing-xl);
    padding-bottom: var(--saico-spacing-xl);
    border-bottom: 1px solid var(--saico-borde-claro);
}

.review-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.review-body {
    display: flex;
    gap: var(--saico-spacing-md);
}

.review-avatar img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.review-content {
    flex: 1;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: var(--saico-spacing-xs);
}

.review-author-info {
    display: flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
}

.review-author {
    font-size: var(--saico-font-base);
    font-weight: var(--saico-font-weight-semibold);
    color: var(--saico-texto-primario);
}

.verified-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    background: var(--saico-primario-light);
    color: var(--saico-primario);
    border-radius: var(--saico-radius-full);
    font-size: var(--saico-font-xs);
    font-weight: var(--saico-font-weight-semibold);
}

.verified-badge svg {
    width: 14px;
    height: 14px;
}

.review-date {
    display: block;
    font-size: var(--saico-font-sm);
    color: var(--saico-texto-terciario);
    margin-bottom: var(--saico-spacing-md);
}

.review-text {
    font-size: var(--saico-font-base);
    line-height: var(--saico-line-height-relaxed);
    color: var(--saico-texto-primario);
}

.review-text p {
    margin-bottom: var(--saico-spacing-sm);
}

.review-text p:last-child {
    margin-bottom: 0;
}

/* ============================================
   SISTEMA DE ESTRELLAS
   ============================================ */
.star-rating {
    display: inline-flex;
    gap: 2px;
}

.star-rating .star {
    width: 20px;
    height: 20px;
    color: #fbbf24;
}

.star-rating .star-full {
    fill: #fbbf24;
}

.star-rating .star-empty {
    stroke: #d1d5db;
    stroke-width: 2px;
}

/* Estrellas más grandes en resumen */
.reviews-summary .star-rating .star {
    width: 32px;
    height: 32px;
}

/* ============================================
   FORMULARIO DE REVIEW
   ============================================ */
#review_form_wrapper {
    margin-top: var(--saico-spacing-2xl);
    padding-top: var(--saico-spacing-2xl);
    border-top: 2px solid var(--saico-borde-claro);
}

.comment-reply-title {
    font-size: var(--saico-font-xl);
    font-weight: var(--saico-font-weight-bold);
    margin-bottom: var(--saico-spacing-xl);
    color: var(--saico-texto-primario);
}

.comment-form p {
    margin-bottom: var(--saico-spacing-lg);
}

.comment-form label {
    display: block;
    margin-bottom: var(--saico-spacing-sm);
    font-weight: var(--saico-font-weight-semibold);
    color: var(--saico-texto-primario);
}

.comment-form input[type="text"],
.comment-form input[type="email"],
.comment-form textarea {
    width: 100%;
    padding: var(--saico-spacing-md);
    border: 2px solid var(--saico-borde-claro);
    border-radius: var(--saico-radius-md);
    font-size: var(--saico-font-base);
    background: var(--saico-bg-primario);
    color: var(--saico-texto-primario);
    transition: border-color var(--saico-transition-fast);
}

.comment-form input:focus,
.comment-form textarea:focus {
    outline: none;
    border-color: var(--saico-primario);
}

.comment-form .required {
    color: var(--saico-error);
}

/* Sistema de estrellas en formulario */
.comment-form-rating {
    margin-bottom: var(--saico-spacing-xl);
}

.stars-input {
    display: flex;
    gap: 8px;
    margin-top: var(--saico-spacing-sm);
}

.stars-input .star {
    width: 32px;
    height: 32px;
    cursor: pointer;
    transition: all var(--saico-transition-fast);
    color: #d1d5db;
    stroke-width: 2px;
}

.stars-input .star:hover,
.stars-input .star.active {
    color: #fbbf24;
    fill: #fbbf24;
    transform: scale(1.1);
}

.form-submit button {
    display: inline-flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
    padding: var(--saico-spacing-md) var(--saico-spacing-xl);
    background: var(--saico-primario);
    color: white;
    border: none;
    border-radius: var(--saico-radius-md);
    font-size: var(--saico-font-base);
    font-weight: var(--saico-font-weight-semibold);
    cursor: pointer;
    transition: all var(--saico-transition-fast);
}

.form-submit button:hover {
    background: var(--saico-primario-hover);
    transform: translateY(-2px);
    box-shadow: var(--saico-shadow-lg);
}

/* ============================================
   MENSAJES
   ============================================ */
.no-reviews,
.review-form-login,
.must-log-in {
    padding: var(--saico-spacing-xl);
    text-align: center;
    background: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-md);
    color: var(--saico-texto-secundario);
}

.must-log-in a,
.review-form-login a {
    color: var(--saico-primario);
    text-decoration: underline;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 768px) {
    .review-body {
        flex-direction: column;
    }

    .review-avatar img {
        width: 48px;
        height: 48px;
    }

    .review-header {
        flex-direction: column;
        gap: var(--saico-spacing-xs);
    }

    .reviews-summary {
        padding: var(--saico-spacing-xl);
    }

    .rating-number {
        font-size: 36px;
    }
}

@media (max-width: 480px) {
    .stars-input .star {
        width: 28px;
        height: 28px;
    }

    .rating-number {
        font-size: 32px;
    }

    .reviews-summary .star-rating .star {
        width: 24px;
        height: 24px;
    }
}
</style>
