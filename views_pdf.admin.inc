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

// Theme is available as an element type (may have additional processing in rendering).
$output .= array(
  '#type' => 'table',
  '#header' => $header,
  '#rows' => $rows,
  '#attributes' => array(
    'id' => 'views_pdf-table',
  ),
);
$output .= drupal_render($table);


  $output .= drupal_render_children($form);

  return $output;

}
