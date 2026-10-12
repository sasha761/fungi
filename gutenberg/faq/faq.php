<?php
$accordion     = get_field('accordion'); // ожидается массив элементов с полями 'title' и 'text'
?>

<?php if ( ! empty( $accordion ) && is_array( $accordion ) ) : ?>
  <div class="c-accordion js-accordion">
    <?php foreach ( $accordion as $item ) :
      $title = isset( $item['title'] ) ? $item['title'] : '';
      $text  = isset( $item['text'] )  ? $item['text']  : '';
      if ( $title && $text ) : ?>
        <div class="c-accordion__block js-accordion__item">
          <div class="c-accordion__block-title js-accordion__heading">
            <span><?php echo esc_html( $title ); ?></span>
            <svg width="44" height="26" aria-hidden="true" focusable="false">
              <use xlink:href="#icon-arrow-bottom"></use>
            </svg>
          </div>
          <div class="c-accordion__block-content js-accordion__list">
            <p><?php echo wp_kses_post( $text ); ?></p>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
