<?php

/**
 * @file
 * Theme function for enhance the views admin interface for PDF options.
 */


/**
 * Theme function to style the table UI.
 *
 * Theme function to style the table UI in the Table Style Settings pages in the
 * view editing form.
 */
function theme_views_pdf_plugin_style_table($variables) {
  $form = $variables['form'];

  $output = \Drupal::service("renderer")->render($form['description_markup']);

  $header = array(
    t('Field'),
    t('Header Style'),
    t('Body Style'),
    t('Position'),
  );

  $rows = array();
  foreach (\Drupal\Core\Render\Element::children($form['info']) as $id) {
    $row   = array();
    $row[] = array(
      'data'  => \Drupal::service("renderer")->render($form['info'][$id]['name']),
      'width' => '20%',
    );
    $row[] = array(
      'data'  => \Drupal::service("renderer")->render($form['info'][$id]['header_style']),
      'width' => '30%',
    );
    $row[] = array(
      'data'  => \Drupal::service("renderer")->render($form['info'][$id]['body_style']),
      'width' => '30%',
    );
    $row[] = array(
      'data'  => \Drupal::service("renderer")->render($form['info'][$id]['position']),
      'width' => '20%',
    );

    $rows[] = $row;
  }

  // @FIXME
// theme() has been renamed to _theme() and should NEVER be called directly.
// Calling _theme() directly can alter the expected output and potentially
// introduce security issues (see https://www.drupal.org/node/2195739). You
// should use renderable arrays instead.
// 
// 
// @see https://www.drupal.org/node/2195739
// $output .= theme('table', array('header' => $header, 'rows' => $rows));

  $output .= drupal_render_children($form);

  return $output;

}
