<?php

// use Drupal\Core\Render\Element;

// preprocess function for first menu nav
function template_preprocess_layoutmenu_bestlayouts_first_menu(&$vars) {
  $vars['items'] = $vars['element']['#items'];
  $vars['children'] = $vars['element']['#children'];
}