<?php

// use Drupal\Core\Render\Element;

// preprocess function for first menu nav
function template_preprocess_layoutmenu_bestlayouts_first_menu(&$vars) {
  $vars['items'] = $vars['element']['#items'];
  $vars['children'] = $vars['element']['#children'];
}

// preprocess function for first menu nav
function template_preprocess_layoutmenu_bestlayouts_dynamiques_headers(&$vars) {
  $vars['attributes'] = $vars['element']['#attributes'];
  $vars['items'] = $vars['element']['#items'];
  $vars['children'] = $vars['element']['#children'];
}

//
function template_preprocess_bestlayouts_form_search_style_merseille(&$vars) {
  if (!empty($vars['element']['actions']['submit'])) {
    $vars['element']['actions']['submit']['#theme_wrappers'][] = 'input_submit_blm';
  }
  if (!empty($vars['element']['keys'])) {
    $vars['element']['keys']['#attributes']['placeholder'] = $vars['element']['keys']['#attributes']['title'];
    if (!empty($vars['element']['keys']['#attributes']['class'])) {
      $vars['element']['keys']['#attributes']['class'][] = 'blm-key-search';
      $vars['element']['keys']['#attributes']['class'][] = 'h3';
    }
    else {
      $vars['element']['keys']['#attributes']['class'] = [
        'blm-key-search',
        'h3'
      ];
    }
  }
}

function template_preprocess_bestlayouts_input_submit_blm(&$vars) {
  if (!empty($vars['element']['#attributes'])) {
    $vars['attributes'] = $vars['element']['#attributes'];
    $vars['value'] = $vars['element']['#value'];
  }
}