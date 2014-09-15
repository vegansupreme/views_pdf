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
    $options['formats']          = array('default' => array());


    return $options;
  }


 /**
   * Render the given style.
   */
  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    
     $options = $this->display->handler->get_field_labels();
    $fields  = $this->display->handler->get_option('fields');
    
    foreach ($options as $field => $option) {
    
          if (isset($fields[$field]['exclude']) && $fields[$field]['exclude'] == 1) {
        continue;
      }
     
     $form['formats'][$field] = array(
        '#type'        => 'fieldset',
        '#title'       => check_plain($option),
        '#collapsed'   => TRUE,
        '#collapsible' => TRUE,
      );
    
    $fonts = array_merge(
      array(
        'default' => t('-- Default --')
      ),
      \Drupal\views_pdf\ViewsPdfBase::getAvailableFontsCleanList());

    $font_styles = array(
      'b' => t('Bold'),
      'i' => t('Italic'),
      'u' => t('Underline'),
      'd' => t('Line through'),
      'o' => t('Overline'),
    );
    
    $align = array(
      'L' => t('Left'),
      'C' => t('Center'),
      'R' => t('Right'),
      'J' => t('Justify'),
    );

    $hyphenate = array(
      'none' => t('None'),
      'auto' => t('Detect automatically'),
    );
    $hyphenate = array_merge(
      $hyphenate,
      \Drupal\views_pdf\ViewsPdfBase::getAvailableHyphenatePatterns()
    );
    
    $relative_elements = array(
      'page'          => t('Page'),
      'header_footer' => t('In header / footer'),
      'last_position' => t('Last Writing Position'),
      'self'          => t('Field: Self'),
    );
    
    $form['columns'] = array(
      '#type' => 'textfield',
      '#title' => t('Number of columns'),
      '#default_value' => $this->options['columns'],
      '#required' => TRUE,
      '#element_validate' => array('views_element_validate_integer'),
    );
    
      $form['formats'][$field]['text'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Text Settings'),
        '#collapsed'   => FALSE,
        '#collapsible' => TRUE,
      );

      $form['formats'][$field]['text']['font_size']   = array(
        '#type'          => 'textfield',
        '#title'         => t('Font Size'),
        '#size'          => 10,
        '#default_value' => isset($this->options['formats'][$field]['text']['font_size']) ? $this->options['formats'][$field]['text']['font_size'] : '',
      );
      $form['formats'][$field]['text']['font_family'] = array(
        '#type'          => 'select',
        '#title'         => t('Font Family'),
        '#required'      => TRUE,
        '#options'       => $fonts,
        '#size'          => 5,
        '#default_value' => isset($this->options['formats'][$field]['text']['font_family']) ? $this->options['formats'][$field]['text']['font_family'] : 'default',
      );
      $form['formats'][$field]['text']['font_style']  = array(
        '#type'          => 'checkboxes',
        '#title'         => t('Font Style'),
        '#options'       => $font_styles,
        '#size'          => 10,
        '#default_value' => !isset($this->options['formats'][$field]['text']['font_style']) ? $this->display->handler->get_option('default_font_style') : $this->options['formats'][$field]['text']['font_style'],
      );
      $form['formats'][$field]['text']['align']       = array(
        '#type'          => 'radios',
        '#title'         => t('Alignment'),
        '#options'       => $align,
        '#default_value' => !isset($this->options['formats'][$field]['text']['align']) ? $this->display->handler->get_option('default_text_align') : $this->options['formats'][$field]['text']['align'],
      );
      $form['formats'][$field]['text']['hyphenate']   = array(
        '#type'          => 'select',
        '#title'         => t('Text Hyphenation'),
        '#options'       => $hyphenate,
        '#description'   => t('If you want to use hyphenation, then you need to download from <a href="@url">ctan.org</a> your needed pattern set. Then upload it to the dir "hyphenate_patterns" in the TCPDF lib directory. Perhaps you need to create the dir first. If you select the automated detection, then we try to get the language of the current node and select an appropriate hyphenation pattern.', array('@url' => 'http://www.ctan.org/tex-archive/language/hyph-utf8/tex/generic/hyph-utf8/patterns/tex')),
        '#default_value' => !isset($this->options['formats'][$field]['text']['hyphenate']) ? $this->display->handler->get_option('default_text_hyphenate') : $this->options['formats'][$field]['text']['hyphenate'],
      );
      $form['formats'][$field]['text']['color']       = array(
        '#type'          => 'textfield',
        '#title'         => t('Text Color'),
        '#description'   => t('If a value is entered without a comma, it will be interpreted as a hexadecimal RGB color. Normal RGB can be used by separating the components by a comma. e.g 255,255,255 for white. A CMYK color can be entered in the same way as RGB. e.g. 0,100,0,0 for magenta.'),
        '#size'          => 20,
        '#default_value' => !isset($this->options['formats'][$field]['text']['color']) ? $this->display->handler->get_option('default_text_color') : $this->options['formats'][$field]['text']['color'],
      );
      
        $form['formats'][$field]['position'] = array(
        '#type'        => 'fieldset',
        '#title'       => t('Position Settings'),
        '#collapsed'   => FALSE,
        '#collapsible' => TRUE,
      );

      $form['formats'][$field]['position']['object'] = array(
        '#type'          => 'select',
        '#title'         => t('Position relative to'),
        '#required'      => FALSE,
        '#options'       => $relative_elements,
        '#default_value' => !empty($this->options['formats'][$field]['position']['object']) ? $this->options['formats'][$field]['position']['object'] : 'last_position',
      );

      $form['formats'][$field]['position']['corner'] = array(
        '#type'          => 'radios',
        '#title'         => t('Position relative to corner'),
        '#required'      => FALSE,
        '#options'       => array(
          'top_left'     => t('Top Left'),
          'top_right'    => t('Top Right'),
          'bottom_left'  => t('Bottom Left'),
          'bottom_right' => t('Bottom Right'),
        ),
        '#default_value' => !empty($this->options['formats'][$field]['position']['corner']) ? $this->options['formats'][$field]['position']['corner'] : 'top_left',
      );

      $relative_elements['field_' . $field] = t('Field: !field', array('!field' => $option));
      
      $form['formats'][$field]['position']['x'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Position X'),
        '#required'      => FALSE,
        '#default_value' => !empty($this->options['formats'][$field]['position']['x']) ? $this->options['formats'][$field]['position']['x'] : '',
      );

      $form['formats'][$field]['position']['y'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Position Y'),
        '#required'      => FALSE,
        '#default_value' => !empty($this->options['formats'][$field]['position']['y']) ? $this->options['formats'][$field]['position']['y'] : '',
      );
      
      
      $form['formats'][$field]['render']              = array(
        '#type'        => 'fieldset',
        '#title'       => t('Render Settings'),
        '#collapsed'   => FALSE,
        '#collapsible' => TRUE,
      );
      $form['formats'][$field]['render']['is_html']   = array(
        '#type'          => 'checkbox',
        '#title'         => t('Render As HTML'),
        '#default_value' => isset($this->options['formats'][$field]['render']['is_html']) ? $this->options['formats'][$field]['render']['is_html'] : 1,
      );

      $form['formats'][$field]['render']['minimal_space'] = array(
        '#type'          => 'textfield',
        '#title'         => t('Minimal Space'),
        '#description'   => t('Specify here the minimal space, which is needed on the page, that the content is placed on the page.'),
        '#default_value' => isset($this->options['formats'][$field]['render']['minimal_space']) ? $this->options['formats'][$field]['render']['minimal_space'] : 1,
      );
    //$this->options['formats'][$field]['position']['x'] = '2';
    //$this->options['formats'][$field]['position']['y'] = '2';
    }
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
   
/*
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
        $this->view->pdf->drawContent($set['group'], $options, $this->view);
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
          $this->view->pdf->drawContent($set['group'], $options, $this->view);
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
*/

 /**
   * Render the style
   */

  function render() {
    $output = '';

    $this->view->numberOfRecords = count($this->view->result);
    $this->view->pdf->drawTable($this->view, $this->options);

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
