<?php 
/*
Plugin Name: datapull
Plugin URI: https://github.com/kimmwiens/datapull
Description: WP Code Review Assignment for Mindshare WordPress Dev position.  How to use:  Add a new post and use the shortcode [datapull] with parameters for postcount (number of posts to be retrieved) and category choice.   Options for postcount are any number 1-5. Category options are "News" or "Code".  Ex. [datapull postcount=2 category="News"]
Version: 1.0
Author: Kimm Wiens
Author URI: http://kimmwiens.com
 */

// Enqueue plugin style-file
$path = ($path = plugins_url( 'css/datapull.css', __FILE__ ));
wp_register_style ( 'namespace', $path );
wp_enqueue_style('namespace');

// create shortcode
add_shortcode('datapull', 'get_json_data');

// return posts from json file
function get_json_data($atts) {
    $search_criteria = shortcode_atts( array(
        'postcount' => 'default_postcount',
        'category' => 'default_category',
    ), $atts );

	// return the json using relative addressing
    $str = file_get_contents($path = plugins_url( 'json/datatopull.json', __FILE__ ));
    
    // decode JSON - remove non-printable characters
    $json = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str), true );

    //get number of posts for user feedback based on shortcode parameters
    $posts_returned = 0;
    $counter = 0;
    foreach ($json['root']['data'] as $items){
        if ($counter <= $search_criteria['postcount'] && $items['terms']['category'][0]['name'] == $search_criteria['category']):
            $posts_returned = $posts_returned + 1;
        endif;
        $counter = $counter + 1;
    }

    $post_pluralization = "posts";
    if ($posts_returned == 1)
        $post_pluralization = "post";
		?>
        <div class="feedback">
            <?php    	
            print_r (($posts_returned . " " .  $post_pluralization . " returned for category: " . $search_criteria['category'] ) . "\n");
            ?>
        </div>
        
    <?php
    // return the posts based on shortcode parameters
    $counter = 0;
    foreach ($json['root']['data'] as $items){
        
        if ($counter <= $search_criteria['postcount'] && $items['terms']['category'][0]['name'] == $search_criteria['category']):
            ?>
            <div class="datapull_post"><h2>
              <?php  print_r ($items['title']);?>
            </h2>
            <?php
            print_r ($items['content']);?>	
			</div><br />
			<?php
        endif;
        $counter = $counter + 1;
    }
}
?>
