<?php
function meet_nav_menu_divs($args = '') {
  $args['link_before'] = '<div>';
  $args['link_after'] = '</div>';
  return $args;
}
add_filter('wp_nav_menu_args', 'meet_nav_menu_divs');

function meet_change_wording($translated_text, $untranslated_text, $domain) {
  if ($untranslated_text === 'Sticky') { return 'Push-to-top'; }
  if ($untranslated_text === 'Public, Sticky') { return 'Public, Push-to-top'; }
  if ($untranslated_text === 'Stick this post to the front page') { return 'Push to the top of post lists'; }
  if ($untranslated_text === 'Category Archives: %s') { return '%s'; }
  if ($untranslated_text === 'Tag Archives: %s') { return '%s'; }
  if (strpos($untranslated_text, 'This entry was posted') === 0) { return ''; }
  if ($untranslated_text === 'Comments are closed.') { return ''; }
  return $translated_text;
}
add_filter('gettext', 'meet_change_wording', 20, 3);

function meet_sort_by_tags_then_title($a, $b) {
  $cmp = strcmp(array_shift(wp_get_post_tags($b->ID))->name, array_shift(wp_get_post_tags($a->ID))->name);
  if ($cmp == 0) { return strcmp($a->post_title, $b->post_title); }
  return $cmp;
}
function meet_sort_by_category_then_title($a, $b) {
  $cmp = strcmp(get_category(array_shift(wp_get_post_categories($a->ID)))->name,
                get_category(array_shift(wp_get_post_categories($b->ID)))->name);
  if ($cmp == 0) { return strcmp($a->post_title, $b->post_title); }
  return $cmp;
}

class MEET_Sort {
  public static $display = array(
    'instructors' => 'meet_sort_by_tags_then_title',
    'faq' =>         'meet_sort_by_category_then_title',
  );
  public static $query = array(
    'staff' =>          'post_name ASC',
    'board' =>          'post_name ASC',
    'people' =>         'post_title ASC',
    'instructor-faq' => 'post_title ASC',
    'student-faq' =>    'post_title ASC',
    'supporter-faq' =>  'post_title ASC',
  );
}

function meet_change_display_sort_order($posts) { // XXX only applied after pagination!
  global $wp_query;
  if ($wp_query->is_category) {
    $sort = MEET_Sort::$display[$wp_query->get_queried_object()->slug];
    if ($sort) { usort($posts, $sort); }
  }
  $sticky = array();
  foreach ($posts as $key => $post) {
    if (is_sticky($post->ID)) {
      $sticky[] = $post;
      unset($posts[$key]);
    }
  }
  return array_merge($sticky, $posts);
}
add_filter('the_posts', 'meet_change_display_sort_order');

function meet_change_query_sort_order($orderby) {
  global $wp_query;
  if ($wp_query->is_category) {
    $sort = MEET_Sort::$query[$wp_query->get_queried_object()->slug];
    if ($sort) { return $sort; }
  }
  return $orderby;
}
add_filter('posts_orderby', 'meet_change_query_sort_order');

function twentyeleven_posted_on() { }

function meet_head() {
  echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>';
  echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>';
  $js = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'js';
  $scripts = array_filter(scandir($js), function($name) {
    return substr_compare($name, '.head.js', -8) === 0;
  });
  foreach ($scripts as $script) {
    echo "<script type=\"text/javascript\">\n";
    @readfile($js . DIRECTORY_SEPARATOR . $script);
    echo "\n</script>\n";
  }
}
add_action('wp_head', 'meet_head', 10, 0);

function meet_copyright() {
  echo '&copy; 2011 MEET';
}
add_action('twentyeleven_credits', 'meet_copyright', 10, 0);

function meet_home_videos() {
  $videos = array();
  foreach (get_bookmarks(array('category_name' => 'Homepage Videos', 'orderby' => 'rating')) as $video) {
    preg_match('/youtube.com\/watch\?v=(\w*)/', $video->link_url, $matches);
    $video->youtube_id = esc_attr($matches[1]);
    $videos[] = $video;
  }
  return $videos;
}

class MEET_Redirect {
  public static $map = array(
    '/^\/board(.html)?$/' => '/supporters/',
    '/^\/ceo(.html)?$/' => '/chief-executive-officer-search/',
    '/^\/contact.html$/' => '/contact/',
    '/^\/different.html$/' => '/different/',
    '/^\/donate.html$/' => '/donate/',
    '/^\/faq(.html)$/' => '/category/faq/',
    '/^\/impact.html$/' => '/impact/',
    '/^\/iap(.html)?$/' => '/iap2009/',
    '/^\/instructors.html$/' => '/instructors/',
    '/^\/people(.html)?$/' => '/category/people/staff/', // XXX /people doesn't do the right thing
    '/^\/programs.html$/' => '/programs/',
    '/^\/projects(.html)?$/' => '/category/projects/',
    '/^\/theBoard(.html)?$/' => '/category/people/board/',
  );
}
function meet_redirect_old_urls() {
  while (list($old, $new) = each(MEET_Redirect::$map)) {
    if (preg_match($old, $_SERVER['REQUEST_URI'])) {
      wp_redirect(site_url($new), 301); exit;
    }
  }
}
add_action('template_redirect', 'meet_redirect_old_urls');
?>
