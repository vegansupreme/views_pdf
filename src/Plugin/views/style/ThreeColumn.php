<?php

/**
 * @file
 * Contains \Drupal\views_pdf\Plugin\views\style\ThreeColumn.
 */

// We can't use name space in views 7.x-x.x
// namespace Drupal\views_pdf\Plugin\views\style;

// use Drupal\views_pdf\ViewsPdfBase;

/**
 * This class holds all the funtionality used for the Three Column style plugin.
 *
 * @ingroup views_style_plugins
 */
class ThreeColumn extends views_plugin_style {


  /**
   * Set default options
   */
  function option_definition() {
    $options = parent::option_definition();

    $options['columns'] = array('default' => '4');
    $options['alignment'] = array('default' => 'horizontal');
    $options['fill_single_line'] = array('default' => TRUE, 'bool' => TRUE);
    $options['summary'] = array('default' => '');
    $options['caption'] = array('default' => '');

    return $options;
  }


 /**
   * Render the given style.
   */
  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    $form['columns'] = array(
      '#type' => 'textfield',
      '#title' => t('Number of columns'),
      '#default_value' => $this->options['columns'],
      '#required' => TRUE,
      '#element_validate' => array('views_element_validate_integer'),
    );
    $form['alignment'] = array(
      '#type' => 'radios',
      '#title' => t('Alignment'),
      '#options' => array('horizontal' => t('Horizontal'), 'vertical' => t('Vertical')),
      '#default_value' => $this->options['alignment'],
      '#description' => t('Horizontal alignment will place items starting in the upper left and moving right. Vertical alignment will place items starting in the upper left and moving down.'),
    );

    $form['fill_single_line'] = array(
      '#type' => 'checkbox',
      '#title' => t('Fill up single line'),
      '#description' => t('If you disable this option, a grid with only one row will have the same number of table cells (<TD>) as items. Disabling it can cause problems with your CSS.'),
      '#default_value' => !empty($this->options['fill_single_line']),
    );

    $form['caption'] = array(
      '#type' => 'textfield',
      '#title' => t('Short description of table'),
      '#description' => t('Include a caption for better accessibility of your table.'),
      '#default_value' => $this->options['caption'],
    );

    $form['summary'] = array(
      '#type' => 'textfield',
      '#title' => t('Table summary'),
      '#description' => t('This value will be displayed as table-summary attribute in the html. Use this to give a summary of complex tables.'),
      '#default_value' => $this->options['summary'],
    );
  }



  /**
   * Render the grouping sets.
   *
   * Plugins may override this method if they wish some other way of handling
   * grouping.
   *
   * @param array $sets
   *   Array containing the grouping sets to render.
   * @param int $level
   *   Integer indicating the hierarchical level of the grouping.
   *
   * @return string
   *   Rendered output of given grouping sets.
   */
  function render_grouping_sets($sets, $level = 0) {

    $output = '';

    $next_level = $level + 1;
    foreach ($sets as $set) {
      $row = reset($set['rows']);
      // Render as a grouping set.
      if (is_array($row) && isset($row['group'])) {
        $field_id = $this->options['grouping'][$level]['field'];
        $options  = array();
        if (isset($this->row_plugin->options['formats'][$field_id])) {
          $options = $this->row_plugin->options['formats'][$field_id];
        }
        $this->view->pdf->drawGridContent($set['group'], $options, $this->view);
        $this->render_grouping_sets($set['rows'], $next_level);
      }
      // Render as a record set.
      else {
        if (!empty($set['group'])) {
          $field_id = $this->options['grouping'][$level]['field'];
          $options  = array();
          if (isset($this->row_plugin->options['formats'][$field_id])) {
            $options = $this->row_plugin->options['formats'][$field_id];
          }
          $this->view->pdf->drawGridContent($set['group'], $options, $this->view);
        }

        if ($this->uses_row_plugin()) {
          foreach ($set['rows'] as $index => $row) {
            $this->view->row_index = $index;
            $set['rows'][$index]   = $this->row_plugin->render($row);
          }
        }
      }
    }

    unset($this->view->row_index);

    return $output;
  }

  /**
   * Attach this view to another display as a feed.
   *
   * Provide basic functionality for all export style views like attaching a
   * feed image link.
   */
  function attach_to($display_id, $path, $title) {
    $display     = $this->view->display[$display_id]->handler;
    $url_options = array();
    $input       = $this->view->get_exposed_input();
    if ($input) {
      $url_options['query'] = $input;
    }

    if (empty($this->view->feed_icon)) {
      $this->view->feed_icon = '';
    }
    $this->view->feed_icon .= theme(
      'views_pdf_icon',
      array(
        'path'    => $this->view->get_url(NULL, $path),
        'title'   => $title,
        'options' => $url_options,
      )
    );
  }

}
