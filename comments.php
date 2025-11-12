<?php
/**
 * Template: Comentarios
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="saico-comentarios">

    <?php if (have_comments()) : ?>

    <!-- Título de comentarios -->
    <h3 class="comentarios-titulo">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <?php
        $comentarios_count = get_comments_number();
        if ($comentarios_count === 1) {
            echo '1 Comentario';
        } else {
            echo esc_html($comentarios_count) . ' Comentarios';
        }
        ?>
    </h3>

    <!-- Lista de comentarios -->
    <ol class="comentarios-lista">
        <?php
        wp_list_comments(array(
            'style'       => 'ol',
            'short_ping'  => true,
            'avatar_size' => 60,
            'callback'    => 'saico_comentario_template',
        ));
        ?>
    </ol>

    <!-- Paginación de comentarios -->
    <?php
    the_comments_pagination(array(
        'prev_text' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 18 9 12 15 6"></polyline></svg> Anterior',
        'next_text' => 'Siguiente <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"></polyline></svg>',
    ));
    ?>

    <?php endif; ?>

    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
    <p class="comentarios-cerrados">Los comentarios están cerrados.</p>
    <?php endif; ?>

    <!-- Formulario de comentarios -->
    <?php
    comment_form(array(
        'title_reply_before' => '<h3 id="reply-title" class="comentario-reply-titulo">',
        'title_reply_after'  => '</h3>',
        'title_reply'        => 'Deja un comentario',
        'comment_notes_before' => '<p class="comentario-notas">Tu dirección de correo electrónico no será publicada. Los campos obligatorios están marcados con <span class="required">*</span></p>',
        'comment_field' => '<p class="comment-form-comment"><label for="comment">Comentario <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required></textarea></p>',
        'class_submit' => 'saico-btn saico-btn-primario',
        'submit_button' => '<button type="submit" class="%3$s">%4$s <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg></button>',
        'submit_field' => '<p class="form-submit">%1$s %2$s</p>',
    ));
    ?>

</div>

<?php
/**
 * Callback personalizado para mostrar comentarios
 */
function saico_comentario_template($comment, $args, $depth) {
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class(empty($args['has_children']) ? 'saico-comentario' : 'parent saico-comentario'); ?>>

        <article id="div-comment-<?php comment_ID(); ?>" class="comentario-body">
            <div class="comentario-avatar">
                <?php
                if (0 !== $args['avatar_size']) {
                    echo get_avatar($comment, $args['avatar_size']);
                }
                ?>
            </div>

            <div class="comentario-contenido">
                <div class="comentario-meta">
                    <div class="comentario-autor">
                        <?php
                        printf(
                            '<b class="fn">%s</b>',
                            get_comment_author_link($comment)
                        );
                        ?>
                        <?php if ('0' == $comment->comment_approved) : ?>
                            <span class="comentario-pendiente">(Pendiente de aprobación)</span>
                        <?php endif; ?>
                    </div>

                    <div class="comentario-metadata">
                        <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                            <time datetime="<?php comment_time('c'); ?>">
                                <?php
                                printf(
                                    '%1$s a las %2$s',
                                    get_comment_date('', $comment),
                                    get_comment_time()
                                );
                                ?>
                            </time>
                        </a>

                        <?php
                        comment_reply_link(
                            array_merge(
                                $args,
                                array(
                                    'add_below' => 'div-comment',
                                    'depth'     => $depth,
                                    'max_depth' => $args['max_depth'],
                                    'before'    => '<span class="comentario-separador">•</span>',
                                    'reply_text' => 'Responder',
                                )
                            )
                        );
                        ?>
                    </div>
                </div>

                <div class="comentario-texto">
                    <?php comment_text(); ?>
                </div>
            </div>
        </article>
    <?php
}
?>

<style>
.saico-comentarios {
    margin-top: var(--saico-spacing-3xl);
    padding: var(--saico-spacing-2xl);
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-xl);
    box-shadow: var(--saico-shadow-md);
}

.comentarios-titulo {
    display: flex;
    align-items: center;
    gap: var(--saico-spacing-md);
    font-size: var(--saico-font-2xl);
    font-weight: var(--saico-font-weight-bold);
    margin-bottom: var(--saico-spacing-xl);
    padding-bottom: var(--saico-spacing-md);
    border-bottom: 2px solid var(--saico-borde-claro);
    color: var(--saico-texto-primario);
}

.comentarios-titulo svg {
    width: 24px;
    height: 24px;
    stroke-width: 2px;
    color: var(--saico-primario);
}

.comentarios-lista {
    list-style: none;
    margin: 0 0 var(--saico-spacing-2xl) 0;
    padding: 0;
}

.saico-comentario {
    margin-bottom: var(--saico-spacing-xl);
}

.comentario-body {
    display: flex;
    gap: var(--saico-spacing-md);
    padding: var(--saico-spacing-lg);
    background-color: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-lg);
}

.comentario-avatar img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
}

.comentario-contenido {
    flex: 1;
}

.comentario-meta {
    margin-bottom: var(--saico-spacing-md);
}

.comentario-autor {
    font-weight: var(--saico-font-weight-bold);
    color: var(--saico-texto-primario);
    margin-bottom: var(--saico-spacing-xs);
}

.comentario-autor a {
    color: var(--saico-primario);
    text-decoration: none;
}

.comentario-autor a:hover {
    text-decoration: underline;
}

.comentario-pendiente {
    color: var(--saico-advertencia);
    font-size: var(--saico-font-sm);
    font-weight: var(--saico-font-weight-normal);
    margin-left: var(--saico-spacing-sm);
}

.comentario-metadata {
    display: flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
    font-size: var(--saico-font-sm);
    color: var(--saico-texto-terciario);
}

.comentario-metadata a {
    color: var(--saico-texto-terciario);
    text-decoration: none;
}

.comentario-metadata a:hover {
    color: var(--saico-primario);
}

.comentario-separador {
    color: var(--saico-borde-medio);
}

.comentario-texto {
    color: var(--saico-texto-secundario);
    line-height: var(--saico-line-height-relaxed);
}

.comentario-texto p {
    margin-bottom: var(--saico-spacing-sm);
}

.comentario-texto p:last-child {
    margin-bottom: 0;
}

/* Comentarios anidados */
.children {
    list-style: none;
    margin-top: var(--saico-spacing-lg);
    margin-left: var(--saico-spacing-2xl);
    padding: 0;
}

.comentarios-cerrados {
    padding: var(--saico-spacing-lg);
    text-align: center;
    background-color: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-md);
    color: var(--saico-texto-secundario);
    font-style: italic;
}

/* Formulario de comentarios */
.comment-respond {
    margin-top: var(--saico-spacing-2xl);
    padding-top: var(--saico-spacing-2xl);
    border-top: 2px solid var(--saico-borde-claro);
}

.comentario-reply-titulo {
    font-size: var(--saico-font-xl);
    font-weight: var(--saico-font-weight-bold);
    margin-bottom: var(--saico-spacing-lg);
    color: var(--saico-texto-primario);
}

.comentario-notas {
    margin-bottom: var(--saico-spacing-lg);
    color: var(--saico-texto-secundario);
    font-size: var(--saico-font-sm);
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
.comment-form input[type="url"],
.comment-form textarea {
    width: 100%;
}

.comment-form .required {
    color: var(--saico-error);
}

.form-submit {
    margin-bottom: 0;
}

.form-submit button {
    display: inline-flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
}

.form-submit button svg {
    transition: transform var(--saico-transition-fast);
}

.form-submit button:hover svg {
    transform: translateX(4px);
}

@media (max-width: 768px) {
    .saico-comentarios {
        padding: var(--saico-spacing-lg);
    }

    .comentario-body {
        flex-direction: column;
    }

    .comentario-avatar img {
        width: 48px;
        height: 48px;
    }

    .children {
        margin-left: var(--saico-spacing-lg);
    }
}

@media (max-width: 480px) {
    .saico-comentarios {
        padding: var(--saico-spacing-md);
    }

    .children {
        margin-left: var(--saico-spacing-md);
    }
}
</style>
