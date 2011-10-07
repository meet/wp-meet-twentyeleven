<?php get_header(); ?>
<div id="primary">
<div id="content" role="main">
<?php query_posts($query_string . '&cat=' . get_cat_ID('home')); ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'content', get_post_format() ); ?>
<?php endwhile; ?>
</div>
</div>

<div id="centerstage">
<div id="center-tabs" class="ui-tabs tabs-above elements-youtube-size">
  <ul class="tabs-above">
    <li><a href="#video"><span>Video</span></a></li>
    <li><a href="#images"><span>Images</span></a></li>
  </ul>
  <div id="video" class="ui-tabs-hide element">
    <div class="ui-tabs tabs-below">
    <?php foreach (meet_home_videos() as $video) { ?>
      <div id="video<?=esc_attr($video->link_id)?>">
        <iframe width="320" height="259" src="http://www.youtube.com/embed/<?=$video->youtube_id?>?rel=0&autohide=1&modestbranding=1&showinfo=0&theme=light" frameborder="0" allowfullscreen></iframe>
      </div>
  <?php } ?>
      <ul class="tabs-below">
      <?php foreach (meet_home_videos() as $video) { ?>
        <li><a href="#video<?=esc_attr($video->link_id)?>"><img src="http://i.ytimg.com/vi/<?=$video->youtube_id?>/1.jpg" height="45" width="60"/><span><?=__($video->link_name)?></span></a></li>
      <?php } ?>
      </ul>
    </div>
  </div>
  <div id="images" class="ui-tabs-hide element">
    <p>Coming soon!</p>
  </div>
</div>
<br/>
<h2>Keep in Touch</h2>
<p>New and exciting things are always happening at MEET. Enter your email and stay informed.</p>
<form action="https://spreadsheets0.google.com/formResponse?formkey=dDhhaGpiTTc0eHdyRm5JQTBlUmZrLVE6MQ&amp;ifq" method="POST">
<p class="horizontal">
<label for="entry_0">Email address:</label><br/>
<input type="text" name="entry.0.single" value="" id="entry_0" size="24"/>
<input type="hidden" name="pageNumber" value="0"/>
<input type="hidden" name="backupCache" value=""/>
<button type="submit" name="submit">Submit</button>
</p>
</form>
<p class="footnote">Your email will remain confidential.</p>
</div>

<div id="secondary" class="widget-area" role="complementary">
<?php dynamic_sidebar( 'sidebar-2' ) ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $('.ui-tabs').tabs();
});
</script>
<?php get_footer(); ?>
