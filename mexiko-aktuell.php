<?php
/*
Plugin Name: Mexiko Aktuell
Plugin URI: http://www.mexiko.mx/
Description: Konfigurierbares Widget, das aktuelle und informative Nachrichten verschiedener Bereiche aus Mexiko bereitstellt. Bereitgestellt von <a href="http://www.mexiko.mx/">http://www.mexiko.mx/</a>
Version: 1.0
Author: Frank Kugler
Author URI: http://www.mexiko.mx/
License: GPL3
*/

function mexikonews()
{
  $options = get_option("widget_mexikonews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Mexiko-Nachrichten',
      'news' => '5',
      'chars' => '30',
      'backlink' => 'nein'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://www.mexiko.mx/feed/'); 
  ?>

<ul>
  <?php 
  // maximale Anzahl an News, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
  // maximale Länge, auf die ein Titel, falls notwendig, gekürzt wird
  $max_length = $options['chars'];
  
  // RSS Elemente durchlaufen 
  $cnt = 0;
  foreach($rss->channel->item as $i) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?>
  <li>
    <?php
    // Titel in Zwischenvariable speichern
    $title = $i->title;
    // Länge des Titels ermitteln
    $length = strlen($title);
    // wenn der Titel länger als die vorher definierte Maximallänge ist,
    // wird er gekürzt und mit "..." bereichert, sonst wird er normal ausgegeben
    if($length > $max_length){
      $title = substr($title, 0, $max_length)."...";
    }
    ?>
    <a href="<?=$i->link?>" target="_blank">
    <?=$title?>
    </a> </li>
  <?php 
    $cnt++;
  } 
  ?>
  <?php if ( $options['backlink'] == "ja" ) {?><li><a href="http://www.mexiko.mx/" title="Mexiko-Nachrichten" target="_blank">Mexiko-Nachrichten</a></li><?php } ?>
</ul>
<?php  
}

function widget_mexikonews($args)
{
  extract($args);
  
  $options = get_option("widget_mexikonews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Mexiko-Nachrichten',
      'news' => '5',
      'chars' => '30',
      'backlink' => 'nein'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  mexikonews();
  echo $after_widget;
}

function mexikonews_control()
{
  $options = get_option("widget_mexikonews");
  if (!is_array($options)){
    $options = array(
      'title' => 'Mexiko-Nachrichten',
      'news' => '5',
      'chars' => '30',
      'backlink' => 'nein'
    );
  }
  
  if($_POST['mexikonews-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['mexikonews-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['mexikonews-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['mexikonews-CharCount']);
    $options['backlink'] = htmlspecialchars($_POST['mexikonews-Backlink']);
    update_option("widget_mexikonews", $options);
  }
?>
<p>
  <label for="mexikonews-WidgetTitle">Widget Titel: </label>
  <input type="text" id="mexikonews-WidgetTitle" name="mexikonews-WidgetTitle" value="<?php echo $options['title'];?>" />
  <br />
  <br />
  <label for="mexikonews-NewsCount">Max. Anzahl Nachrichten: </label>
  <input type="text" id="mexikonews-NewsCount" name="mexikonews-NewsCount" value="<?php echo $options['news'];?>" />
  <br />
  <br />
  <label for="mexikonews-CharCount">Max. Anzahl Zeichen: </label>
  <input type="text" id="mexikonews-CharCount" name="mexikonews-CharCount" value="<?php echo $options['chars'];?>" />
  <br />
  <br />
  <label for="mexikonews-Backlink">Backlink anzeigen: </label>
  <select name="mexikonews-Backlink"  id="mexikonews-Backlink" >
    <option value="nein"<?php if ( $options['backlink'] != "ja" ) {?> selected="selected"<?php } ?>>nein</option>
    <option value="ja"<?php if ( $options['backlink'] == "ja" ) {?> selected="selected"<?php } ?>>ja</option>
  </select>
  <br />
  <br />
  <input type="hidden" id="mexikonews-Submit"  name="mexikonews-Submit" value="1" />
</p>
<?php
}

function mexikonews_init()
{
  register_sidebar_widget(__('Mexiko-Nachrichten'), 'widget_mexikonews');    
  register_widget_control('Mexiko-Nachrichten', 'mexikonews_control', 300, 200);
}
add_action("plugins_loaded", "mexikonews_init");
?>
