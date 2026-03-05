<?php
/**
 * Single Paseo (CPT: paseo) — SIN ACF (solo CPT)
 * Archivo: single-paseo.php
 */
if ( ! defined('ABSPATH') ) exit;

get_header();

// Astra: opcional
remove_action( 'astra_entry_header', 'astra_post_meta', 10 );

$titulo = get_the_title();

/* ===== 1) Imagen destacada ===== */
$img_url = '';
if ( has_post_thumbnail() ) {
  $img_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
}

/* ===== 2) Contenido / Lead ===== */
$raw_content = get_the_content();
$content_html = apply_filters('the_content', $raw_content);

if ( has_excerpt() ) {
  $lead = get_the_excerpt();
} else {
  $lead = wp_trim_words( wp_strip_all_tags( $raw_content ), 26 );
}

/* ===== 3) Helpers: extraer secciones por encabezado ===== */
function ps_extract_section_text($content, $heading){
  $heading = preg_quote($heading, '/');
  $pattern = '/<h[2-4][^>]*>\s*' . $heading . '\:?\s*<\/h[2-4]>\s*(.*?)\s*(?=<h[2-4][^>]*>|$)/is';
  if ( preg_match($pattern, $content, $m) ) {
    $text = trim( wp_strip_all_tags( $m[1] ) );
    return $text;
  }
  return '';
}

function ps_extract_section_list_items($content, $heading){
  $heading = preg_quote($heading, '/');
  $pattern = '/<h[2-4][^>]*>\s*' . $heading . '\:?\s*<\/h[2-4]>\s*(.*?)\s*(?=<h[2-4][^>]*>|$)/is';
  if ( preg_match($pattern, $content, $m) ) {
    $section_html = $m[1];
    if ( preg_match('/<ul[^>]*>(.*?)<\/ul>/is', $section_html, $ul) ) {
      preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $ul[0], $lis);
      $items = [];
      foreach ($lis[1] as $li){
        $t = trim( wp_strip_all_tags($li) );
        if ($t !== '') $items[] = $t;
      }
      return $items;
    }
  }
  return [];
}



/* ===== 4) Datos sacados del contenido (sin ACF) ===== */
$cpt_duracion  = ps_extract_section_text($content_html, 'Duración');
$cpt_precio    = ps_extract_section_text($content_html, 'Precio');
$cpt_idioma    = ps_extract_section_text($content_html, 'Idioma');
$cpt_encuentro = ps_extract_section_text($content_html, 'Encuentro');

$incluye_items   = ps_extract_section_list_items($content_html, 'Incluye');
$noincluye_items = ps_extract_section_list_items($content_html, 'No incluye');
?>

<main class="ps-wrap ps-single">

  <!-- HERO -->
  <header class="ps-hero" aria-label="Cabecera del paseo">
    <div class="ps-hero__media" style="<?php echo $img_url ? 'background-image:url(' . esc_url($img_url) . ');' : ''; ?>">
      <?php if ( ! $img_url ): ?>
        <div class="ps-hero__placeholder" aria-hidden="true"></div>
      <?php endif; ?>
      <span class="ps-hero__badge">Paseo guiado · Sevilla</span>
    </div>

    <div class="ps-hero__content">
      <h1 class="ps-title"><?php echo esc_html($titulo); ?></h1>

      <?php if ( ! empty($lead) ): ?>
        <p class="ps-lead"><?php echo esc_html($lead); ?></p>
      <?php endif; ?>

      <div class="ps-hero__actions">
        <a class="ps-btn ps-btn--primary" href="#reserva">Reservar ahora</a>
        <a class="ps-btn ps-btn--ghost" href="/paseo">Ver todos los paseos</a>
      </div>

      <ul class="ps-pills" aria-label="Datos clave">
        <?php if ( ! empty($cpt_duracion) ): ?><li class="ps-pill">⏱ <?php echo esc_html($cpt_duracion); ?></li><?php endif; ?>
        <?php if ( ! empty($cpt_precio) ): ?><li class="ps-pill">💶 <?php echo esc_html($cpt_precio); ?></li><?php endif; ?>
        <?php if ( ! empty($cpt_idioma) ): ?><li class="ps-pill">🗣 <?php echo esc_html($cpt_idioma); ?></li><?php endif; ?>
      </ul>
    </div>
  </header>

  <section class="ps-grid" aria-label="Contenido del paseo">
    <article class="ps-card ps-card--main">
      <div class="ps-card__body">
        <h2 class="ps-h2">Descripción</h2>

        <?php if ( trim(wp_strip_all_tags($raw_content)) !== '' ): ?>
          <div class="ps-prose"><?php echo $content_html; ?></div>
        <?php else: ?>
          <p class="ps-muted">Escribe la descripción en el editor del paseo.</p>
        <?php endif; ?>
      </div>
    </article>

    <aside class="ps-card ps-card--aside" aria-label="Ficha rápida">
      <div class="ps-card__body">
        <h2 class="ps-h2">Ficha rápida</h2>

        <dl class="ps-meta">
          <div class="ps-meta__row"><dt>Duración</dt><dd><?php echo $cpt_duracion ? esc_html($cpt_duracion) : '—'; ?></dd></div>
          <div class="ps-meta__row"><dt>Precio</dt><dd><?php echo $cpt_precio ? esc_html($cpt_precio) : '—'; ?></dd></div>
          <div class="ps-meta__row"><dt>Idioma</dt><dd><?php echo $cpt_idioma ? esc_html($cpt_idioma) : '—'; ?></dd></div>
          <div class="ps-meta__row"><dt>Encuentro</dt><dd><?php echo $cpt_encuentro ? esc_html($cpt_encuentro) : '—'; ?></dd></div>
        </dl>

        <div class="ps-divider" aria-hidden="true"></div>

        <h3 class="ps-h3">Incluye</h3>
        <?php if ( $incluye_items ): ?>
          <ul class="ps-list"><?php foreach($incluye_items as $it){ echo '<li>' . esc_html($it) . '</li>'; } ?></ul>
        <?php else: ?>
          <p class="ps-muted">En el contenido, crea “Incluye” + una lista.</p>
        <?php endif; ?>

        <div class="ps-divider" aria-hidden="true"></div>

        <h3 class="ps-h3">No incluye</h3>
        <?php if ( $noincluye_items ): ?>
          <ul class="ps-list ps-list--danger"><?php foreach($noincluye_items as $it){ echo '<li>' . esc_html($it) . '</li>'; } ?></ul>
        <?php else: ?>
          <p class="ps-muted">En el contenido, crea “No incluye” + una lista.</p>
        <?php endif; ?>

        <div class="ps-cta" id="reserva">
          <p class="ps-note">Reserva rápida. Confirmación por email.</p>
          <a class="ps-btn ps-btn--primary ps-btn--block" href="#reserva">Reservar ahora</a>
          <p class="ps-micro"a href="/contact">¿Dudas? Escríbenos desde Contacto.</a></p>
        </div>
      </div>
    </aside>
  </section>

</main>

<?php get_footer();