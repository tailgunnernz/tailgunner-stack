<?php
/**
 * Body, heading and link typography. Heading sizes use per-breakpoint settings,
 * so their {{dc:theme:*}} tags resolve to var()s that Pro sets per breakpoint.
 */

defined( 'ABSPATH' ) || exit;
?>
/* Body
// ========================================================================== */

html {
  line-height: {{dc:theme:tg_body_line_height}};
  font-weight: {{dc:theme:x_body_font_weight_selection destination="font-weight" family="{{dc:theme:x_body_font_family_selection}}"}};
}

body,
input,
button,
select,
textarea {
  font-family: {{dc:theme:x_body_font_family_selection destination="font-family"}};
  color: {{dc:theme:x_body_font_color}};
}

small,
.text-small {
  font-size: 0.64em !important;
}


/* Headings
// ========================================================================== */

h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, blockquote {
  color: {{dc:theme:x_headings_font_color}};
}

h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6<?php echo cs_headline_selectors(); ?> {
  font-family: {{dc:theme:x_headings_font_family_selection destination="font-family"}};
  font-weight: {{dc:theme:x_headings_font_weight_selection destination="font-weight" family="{{dc:theme:x_headings_font_family_selection}}"}};
}

/* h6 keeps the body line-height, as Pro Starter always has. */
h1, h2, h3, h4, h5 {
  line-height: {{dc:theme:tg_headings_line_height}} !important;
}

h1, .h1 { letter-spacing: {{dc:theme:x_h1_letter_spacing unit="em"}}; }
h2, .h2 { letter-spacing: {{dc:theme:x_h2_letter_spacing unit="em"}}; }
h3, .h3 { letter-spacing: {{dc:theme:x_h3_letter_spacing unit="em"}}; }
h4, .h4 { letter-spacing: {{dc:theme:x_h4_letter_spacing unit="em"}}; }
h5, .h5 { letter-spacing: {{dc:theme:x_h5_letter_spacing unit="em"}}; }
h6, .h6 { letter-spacing: {{dc:theme:x_h6_letter_spacing unit="em"}}; }


/* Heading sizes
// ========================================================================== */

.text-size-h0.x-text.x-content,
.text-size-h0-primary[class*="x-"] [class*="-text"] [class*="-text-primary"] {
  font-size: {{dc:theme:tg_h0_font_size}};
}

h1,
.x-text-headline .x-text-content h1.x-text-content-text-primary,
.text-size-h1.x-text.x-content,
.text-size-h1-primary[class*="x-"] [class*="-text"] [class*="-text-primary"] {
  font-size: {{dc:theme:tg_h1_font_size}};
}

h2,
.x-text-headline .x-text-content h2.x-text-content-text-primary,
.text-size-h2.x-text.x-content,
.text-size-h2-primary[class*="x-"] [class*="-text"] [class*="-text-primary"] {
  font-size: {{dc:theme:tg_h2_font_size}};
}

h3,
.x-text-headline .x-text-content h3.x-text-content-text-primary,
.text-size-h3.x-text.x-content,
.text-size-h3-primary[class*="x-"] [class*="-text"] [class*="-text-primary"] {
  font-size: {{dc:theme:tg_h3_font_size}};
}

h4,
.text-size-h4,
.x-text-headline .x-text-content h4.x-text-content-text-primary,
.text-size-h4.x-text.x-content,
.text-size-h4-primary[class*="x-"] [class*="-text"] [class*="-text-primary"] {
  font-size: {{dc:theme:tg_h4_font_size}};
}

h5,
.x-text-headline .x-text-content h5.x-text-content-text-primary,
.text-size-h5.x-text.x-content,
p.text-size-h5,
.text-size-h5-primary[class*="x-"] [class*="-text"] [class*="-text-primary"] {
  font-size: {{dc:theme:tg_h5_font_size}};
}

h6,
.x-text-headline .x-text-content h6.x-text-content-text-primary,
.text-size-h6.x-text.x-content,
.text-size-h6-primary[class*="x-"] [class*="-text"] [class*="-text-primary"] {
  font-size: {{dc:theme:tg_h6_font_size}};
}


/* Links
// ========================================================================== */

a {
  color: {{dc:theme:x_site_link_color}};
}

a:hover {
  color: {{dc:theme:x_site_link_color_hover}};
}

a:not(.x-anchor):not(.x-cell):not(.x-col):not(.x-slide):not(.wp-block-button__link):not(.no-decoration):not(.wc-block-components-button):not(.ajax_add_to_cart):not(.dgwt-wcas-suggestion):not(.woocommerce-Button) {
  color: inherit;
  text-decoration: underline;
}
