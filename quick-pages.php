<?php
/*
	Plugin Name: Quick Pages
	Plugin URI: https://github.com/snaptortoise/wp-quick-pages
	Description: Quickly add blank pages with hierarchies
	Version: 1.0
	Author: Snaptortoise
	Author URI: http://snaptortoise.com
*/

add_action('admin_menu', 'admin_init');

function admin_init(){
	add_pages_page("WP-Quick-Pages", "WP-Quick-Pages", "read", "wp-quick-pages", array('WPQuickPages', 'quick_pages'));
}



class WPQuickPages {

	function quick_pages() {
		WPQuickPages::header("Quick Pages");		
		?>
<div class="inner-sidebar">
		<p>
			You can quickly add blank, published pages with hierarchies by following this simple format:
		</p>

<pre>
Home
Stories
Store
- Software
- Freebies
About
- Team
-- Jane Doe
-- John Doe
- Bio
Contact
- Form
</pre>

</div>
<div id="post-body" class="has-right-sidebar">
<div id="post-body-content">
	<div id="wp-content-wrap" class="wp-editor-wrap html-active">
	<div id="wp-content-editor-container" class="wp-editor-container">
			<form method='post' action=''>
				<textarea name="pages" id="" cols="40" rows="20" class="wp-editor-area"></textarea>
				<p><input type="submit" value="Create these pages" class="button-primary"/></p>
			</form>
	</div>
	</div>
			<?php
			if ($_POST["pages"]) {
				echo "<h2>Results</h2>";
				$pages = (explode("\n",$_POST["pages"]));
				$site = array();

				foreach ($pages as $key => $page) {
					$page = trim($page);
					$parent = 0;
					$parent_id=0;
					preg_match("/^[\-]+/", $page, $child);
					
					if (@$child[0]) {
						$depth = strlen($child[0])-1;	
						$page = trim(substr($page,$depth+1));
						
						// cycle through and find parent
						for ($i = $key; $i--; $i >= 0) {
							// if we find it...
							$pattern = "/^[\-]{".$depth."}[^\-]/";		
								
							if ((preg_match($pattern, $pages[$i], $test) && $depth > 0) || ($depth== 0 && substr($pages[$i],0,1) != "-") )  {																	
								// Get the WordPress page ID
								$parent = $site[$i]["post_title"];
								$parent_id = $site[$i]["id"];
								$parent_key = $i;
								$i=false;			
							}			
						}	
					}

				$page_array = array(
					"post_title" => $page,				
					"post_parent" => $parent_id,
					"post_status" => "publish",
					"post_type" => "page"
					);

				$post_id = wp_insert_post($page_array, $wp_error);							
				$page_array["id"] = $post_id;
				$page_array["parent_key"] = $parent_key;
				$site[$key] = $page_array;
				
				?>
				<p>
					Creating <strong>
					<?php if ($parent_id > 0): ?>
					<?= $parent ?> &raquo;
					<?php endif;?>
					<?= $page ?>
					</strong>
				</p>
				<?php
				}
				
			}

			echo "</div></div>";
		WPQuickPages::footer();
	}


	function header($title) {
		?><div class="wrap columns-2">
			<div id="icon-edit-pages" class="icon32"><br></div>
			<h2><?= $title ?></h2><br/><?php
	}

	function footer() {
		?>
		</div>
		</div>
		<?php
	}
}
