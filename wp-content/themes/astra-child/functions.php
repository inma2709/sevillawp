<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'astra-theme-css' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION
add_filter( 'astra_post_meta', function( $output ) {
    if ( is_singular('rutas') ) {
        return '';
    }
    return $output;
});

function ps_paseos_cards_shortcode() {

  ob_start();

  $query = new WP_Query([
    'post_type' => 'paseo',
    'posts_per_page' => -1
  ]);

  if ($query->have_posts()) {
    echo '<div class="ps-cards">';
    while ($query->have_posts()) {
      $query->the_post();
      ?>
      <div class="ps-card-item">
        <a href="<?php the_permalink(); ?>">
          <h3><?php the_title(); ?></h3>
        </a>
      </div>
      <?php
    }
    echo '</div>';
  }

  wp_reset_postdata();

  return ob_get_clean();
}
add_shortcode('paseos_cards', 'ps_paseos_cards_shortcode');

/* Agrega plantilla de bloques para el post type "paseo" */

function ps_paseo_template() {
    $post_type_object = get_post_type_object('paseo');

    $post_type_object->template = array(
        array('core/paragraph', array(
            'placeholder' => 'Escribe aquí la descripción del paseo...'
        )),
        array('core/heading', array(
            'level' => 2,
            'content' => 'Duración'
        )),
        array('core/paragraph', array(
            'placeholder' => 'Ej: 2 horas'
        )),
        array('core/heading', array(
            'level' => 2,
            'content' => 'Precio'
        )),
        array('core/paragraph', array(
            'placeholder' => 'Ej: Desde 18€'
        )),
        array('core/heading', array(
            'level' => 2,
            'content' => 'Idioma'
        )),
        array('core/paragraph', array(
            'placeholder' => 'Ej: Español e inglés'
        )),
        array('core/heading', array(
            'level' => 2,
            'content' => 'Encuentro'
        )),
        array('core/paragraph', array(
            'placeholder' => 'Lugar de encuentro'
        )),
        array('core/heading', array(
            'level' => 2,
            'content' => 'Incluye'
        )),
        array('core/list'),
        array('core/heading', array(
            'level' => 2,
            'content' => 'No incluye'
        )),
        array('core/list')
    );
}
add_action('init', 'ps_paseo_template');


/*encolamos archivo de estilos solo para el archivo de paseos*/

function ps_enqueue_paseos_archive_styles() {

  // Carga SOLO en el archivo /paseo/
  if ( is_post_type_archive('paseo') ) {
    wp_enqueue_style(
      'ps-paseos-archive-style',
      get_stylesheet_directory_uri() . '/assets/css/paseos-archive.css',
      array(),
      '1.0'
    );
  }

}
add_action('wp_enqueue_scripts', 'ps_enqueue_paseos_archive_styles');