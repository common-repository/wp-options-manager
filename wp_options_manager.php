<?php
/*
Plugin Name: WP-Options-Manager
Plugin URI: http://wordpress.org/extend/plugins/wp-options-manager/
Version: 0.06
Author: Marc Schieferdecker
Author URI: http://www.das-motorrad-blog.de
Description: With this plugin you can browse, delete and debug the entrys of your WordPress options database table. Important: Only the administrator user should have access to this plugin and should know what he is doing!
License: GPL
*/

define( 'WPOM_PLUGIN_PATH', dirname(__FILE__) . '/../' );
define( 'WPOM_DATAFILE', str_replace( 'plugins/wp-options-manager/../../', '', dirname(__FILE__) . '/../../uploads/.wpom.dat' ) );
define( 'WPOM_DATADIR', dirname( WPOM_DATAFILE ) );

// The main plugin class
class wp_options_manager
{
	var $db;
	var $tp;
	var $wp_default_options;
	var $wp_popular_plugins;
	var $wpom_PluginOptions;
	var $wpom_PluginOptionsName;

	// Construct
	function __construct()
	{
		// Get db object
		global $wpdb;
		$this -> db =& $wpdb;

		// Get table prefix
		global $table_prefix;
		$this -> tp = $table_prefix;

		// Set wp default options
		$this -> wp_default_options = array( 'siteurl', 'blogname', 'blogdescription', 'users_can_register', 'admin_email', 'start_of_week', 'use_balanceTags', 'use_smilies', 'require_name_email', 'comments_notify', 'posts_per_rss', 'rss_excerpt_length', 'rss_use_excerpt', 'mailserver_url', 'mailserver_login', 'mailserver_pass', 'mailserver_port', 'default_category', 'default_comment_status', 'default_ping_status', 'default_pingback_flag', 'default_post_edit_rows', 'posts_per_page', 'what_to_show', 'date_format', 'time_format', 'links_updated_date_format', 'links_recently_updated_prepend', 'links_recently_updated_append', 'links_recently_updated_time', 'comment_moderation', 'moderation_notify', 'permalink_structure', 'gzipcompression', 'hack_file', 'blog_charset', 'moderation_keys', 'active_plugins', 'home', 'category_base', 'ping_sites', 'advanced_edit', 'comment_max_links', 'gmt_offset', 'default_email_category', 'recently_edited', 'use_linksupdate', 'template', 'stylesheet', 'comment_whitelist', 'blacklist_keys', 'comment_registration', 'open_proxy_check', 'rss_language', 'html_type', 'use_trackback', 'default_role', 'db_version', 'wp_user_roles', 'uploads_use_yearmonth_folders', 'upload_path', 'secret', 'blog_public', 'default_link_category', 'show_on_front', 'default_link_category', 'cron', 'doing_cron', 'sidebars_widgets', 'widget_pages', 'widget_calendar', 'widget_archives', 'widget_meta', 'widget_categories', 'widget_recent_entries', 'widget_text', 'widget_rss', 'widget_recent_comments', 'widget_wholinked', 'widget_polls', 'tag_base', 'page_on_front', 'page_for_posts', 'page_uris', 'page_attachment_uris', 'show_avatars', 'avatar_rating', 'upload_url_path', 'thumbnail_size_w', 'thumbnail_size_h', 'thumbnail_crop', 'medium_size_w', 'medium_size_h', 'dashboard_widget_options', 'current_theme', 'auth_salt', 'avatar_default', 'enable_app', 'enable_xmlrpc', 'logged_in_salt', 'recently_activated', 'random_seed', 'large_size_w', 'large_size_h', 'image_default_link_type', 'image_default_size', 'image_default_align', 'close_comments_for_old_posts', 'close_comments_days_old', 'thread_comments', 'thread_comments_depth', 'page_comments', 'comments_per_page', 'default_comments_page', 'comment_order', 'use_ssl', 'sticky_posts', 'dismissed_update_core', 'update_themes', 'nonce_salt', 'update_core', 'uninstall_plugins', 'wporg_popular_tags', 'stats_options', 'stats_cache', 'rewrite_rules', 'update_plugins', 'category_children', 'timezone_string', 'can_compress_scripts', 'db_upgraded', '_transient_doing_cron', '_transient_plugins_delete_result_1', '_transient_plugin_slugs', '_transient_random_seed', '_transient_rewrite_rules', '_transient_update_core', '_transient_update_plugins', '_transient_update_themes', 'widget_search' );

		// Set wp popular plugins
		$this -> wp_popular_plugins = array(	'a2pPlugin_' => 'article2pdf',
							'wpom_setup_information' => 'WP Options Manager',
							'wowcdwidget_multi' => 'WoW Display Character',
							'wowcdPlugin_AdminOptions' => 'WoW Display Character',
							'aiosp' => 'All in One SEO Pack',
							'akismet' => 'Akismet',
							'wordtube' => 'wordTube',
							'tadv' => 'TinyMCE Advanced',
							'sm_' => 'Google XML Sitemap Generator',
							'simpletags' => 'Simple Tags',
							'poll_' => 'WP-Polls',
							'random-posts' => 'Random posts',
							'ngg_' => 'NextGEN Gallery',
							'widget_nggslideshow' => 'NextGEN Gallery',
							'linklift_' => 'LinkLift',
							'lcb_' => 'List Draft Posts',
							'exec-php' => 'Exec-PHP',
							'fs_' => 'FeedStats',
							'dbmanager_' => 'WP-DBManager',
							'events_calendar' => 'Events Calendar',
							'EventsCalendar' => 'EventsCalendar',
							'wordpress_api_key' => 'WordPress.org plugins',
							'widget_skypewidget' => 'Skype widget',
							'widget_multi_pages' => 'Multi Pages widget',
							'kbrss' => 'KB Advanced RSS Widget',
							'views_options' => 'WP-PostViews',
							'umapper' => 'UMapper',
							'similar-posts' => 'Similar Posts',
							'pc_ignored_plugins' => 'Plugin Central',
							'rss_' => 'internal rss cache (safe to delete if bloated)',
							'wassup_settings' => 'Wassup',
							'_transient_' => 'WordPress internal',
							'widget_recent-' => 'Recent Posts Widget',
							'widget_tag_cloud' => 'Simple Tags',
							'widget_twitter' => 'Twitter for WordPress'
						);

		// Plugin default options
		$this -> wpom_PluginOptionsName = 'wpom_setup_information';
		$this -> wpom_PluginOptions =	array(	'autocollect' => 'false'
						);
	}
	// PHP4 compatibe construct (please update to PHP 5 soon! ;) )
	function wp_options_manager() { $this -> __construct(); }

	// Admin interface
	function wpom_AdminPage()
	{
		// Load options to collect that the options are accessed :-)
		$this -> _get_options();

		// Language textdomain
		if( function_exists( 'load_plugin_textdomain' ) ) {
			load_plugin_textdomain( 'wpom', 'wp-content/plugins/wp-options-manager' );
		}
		// Set panel to open
		if( $_REQUEST[ 'wpom_admin_action' ] == 'pluginsearch' )
			$open = 3;
		else
		if( $_POST[ 'wpom_admin_action' ] == 'autosearch' )
			$open = 2;
		else
			$open = 1;

		// Container
		print "<div class=\"wrap\">\n";
		print "<h2>" . __( "WP Options Manager", "wpom" ) . "</h2>\n";
		print "<br class=\"clear\"/>";

		// AJAX: Delete option
		if( $_REQUEST[ 'wpom_admin_action' ] == 'delete' )
		{
			$id = intval( $_REQUEST[ 'id' ] );
			$this -> db -> query( "DELETE FROM {$this->tp}options WHERE option_id='$id' LIMIT 1;" );
			// Quit process, we are done
			exit();
		}

		// AJAX: Debug option value
		if( $_REQUEST[ 'wpom_admin_action' ] == 'debug' )
		{
			print "<!-- WPOM AJAX CONTENT -->";	// Use markers, to keep all functions in this file
			$id = intval( $_REQUEST[ 'id' ] );
			$row = $this -> db -> get_row( "SELECT option_value FROM {$this->tp}options WHERE option_id='$id' LIMIT 1;" );
			if( is_object( $row ) )
			{
				$debug = print_r( unserialize( $row -> option_value ), true );
				// If is debugable print debug string
				if( !empty( $debug ) )
				{
					print "<pre>";
					print $debug;
					print "</pre>";
				}
				else
				if( is_numeric( $row -> option_value ) && strlen( $row -> option_value ) >= 8 )
				{
					print strftime( '%x %H:%I', $row -> option_value );
				}
				// Not debugable? Then print original string
				else	print __( "Not debugable:", "wpom" ) . " <i>" . $row -> option_value . "</i>";
			}
			else	print __( "Error. Row not found.", "wpom" );
			print "<!-- WPOM AJAX CONTENT -->";	// Use markers, to keep all functions in this file
			// Quit process, we are done
			exit();
		}

		// Delete multiple options
		if( $_POST[ 'wpom_admin_action' ] == 'deletemultiple' )
		{
			$delete_arr = $_POST[ 'wpom_deletelist' ];
			if( is_array( $delete_arr ) && count( $delete_arr ) )
			{
				foreach( $delete_arr AS $delete_option_id )
					$this -> db -> query( "DELETE FROM {$this->tp}options WHERE option_id='$delete_option_id' LIMIT 1;" );
				print "<div id=\"message\" class=\"updated fade\"><p><strong>" . __( "All selected options were deleted.", "wpom" ) . "</strong></p></div>";
			}
			else	print "<div id=\"message\" class=\"updated fade\"><p><strong>" . __( "That was easy. You selected no options for deletion.", "wpom" ) . "</strong></p></div>";
		}

		// Save setup
		if( $_POST[ 'wpom_admin_action' ] == 'setoptions' )
		{
			$autocollect = ($_POST[ 'autocollect' ] == 'true' ? 'true' : 'false');
			$this -> wpom_PluginOptions[ 'autocollect' ] = $autocollect;
			$this -> _set_options();
			if( $autocollect != 'true' && file_exists( WPOM_DATAFILE ) )
				unlink( WPOM_DATAFILE );
			$open = 5;
			print "<div id=\"message\" class=\"updated fade\"><p><strong>" . __( "Setup saved.", "wpom" ) . "</strong></p></div>";
		}


		// Output search options form
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<form name=\"wpom_searchform\" method=\"POST\" action=\"" . $_SERVER[ "REQUEST_URI" ] . "\" enctype=\"multipart/form-data\">\n";
		print "<input type=\"hidden\" name=\"wpom_admin_action\" value=\"search\"/>\n";
		print "<h3>" . __( "Search options in the database table", "wpom" ) . "</h3>\n";
		print "<div class=\"inside\">\n";
		print "<table class=\"widefat post fixed\">";
		print "<tr>";
		print "<td>";
		print "<h4>" . __( "Please enter a search term", "wpom" ) . "</h4>";
		print "<p><input type=\"text\" name=\"wpom_searchterm\" size=\"20\" maxlength=\"80\" value=\"" . htmlentities( $_POST[ 'wpom_searchterm' ] ) . "\"/></p>\n";
		print "</td>";
		print "<td>";
		print "<h4>" . __( "Search in option values also?", "wpom" ) . "</h4>";
		print "<p><select name=\"wpom_search_values\">\n";
			print "<option value=\"false\"" . ($_POST[ 'wpom_search_values' ] == 'false' ? "selected=\"selected\"" : "") . ">" . __( "No, please not", "wpom" ) . "</option>\n";
			print "<option value=\"true\"" . ($_POST[ 'wpom_search_values' ] == 'true' ? "selected=\"selected\"" : "") . ">" . __( "Yes, do that!", "wpom" ) . "</option>\n";
		print "</select></p>\n";
		print "</td>";
		print "<td>";
		print "<h4>" . __( "Hide WP default options?", "wpom" ) . "</h4>";
		print "<p><select name=\"wpom_hide_default_options\">\n";
			print "<option value=\"true\"" . ($_POST[ 'wpom_hide_default_options' ] == 'true' ? "selected=\"selected\"" : "") . ">" . __( "Yes, do that!", "wpom" ) . "</option>\n";
			print "<option value=\"false\"" . ($_POST[ 'wpom_hide_default_options' ] == 'false' ? "selected=\"selected\"" : "") . ">" . __( "No, please not", "wpom" ) . "</option>\n";
		print "</select></p>\n";
		print "</td>";
		print "<td>";
		print "<p class=\"submit\"><br/><input type=\"submit\" name=\"wpom_submitsearch\" value=\"" . __( "Search options", "wpom" ) . "\"/></p>\n";
		print "</td>";
		print "</tr>";
		print "<tr><td colspan=\"4\">" . __( "Hint: You don't have to enter a search term. Use a blank search term and search with \"hide default entrys\" set to \"yes\" to see all options not from WordPress itself.", "wpom" ) . "</td></tr>";
		print "</table>";
		print "</form>\n";
		// Run search
		if( $_POST[ 'wpom_admin_action' ] == 'search' )
		{
			$term = strtolower( mysql_real_escape_string( $_POST[ 'wpom_searchterm' ] ) );
			if( $_POST[ 'wpom_search_values' ] == 'false' )
				$rowsfound = $this -> db -> query( "SELECT * FROM {$this->tp}options WHERE option_name LIKE '%$term%' ORDER BY blog_id ASC, option_name ASC;" );
			else
				$rowsfound = $this -> db -> query( "SELECT * FROM {$this->tp}options WHERE option_name LIKE '%$term%' OR option_value LIKE '%$term%' ORDER BY blog_id ASC, option_name ASC;" );
			if( $rowsfound )
			{
				// Output hits
				print "<p><strong>" . __( "Number of entrys found:", "wpom" ) . " $rowsfound</strong></p>\n";
				if( $_POST[ 'wpom_hide_default_options' ] == 'true' )
					print "<p>" . __( "Maybe not all of the entrys will be displayed, because you activated the \"Hide default options\" feature.", "wpom" ) . "</p>";
				// Output
				print "<form name=\"wpom_deletemultiple1\" method=\"POST\" action=\"" . $_SERVER[ "REQUEST_URI" ] . "\" enctype=\"multipart/form-data\" onsubmit=\"return confirm('" . __( "Are you really sure that you want to delete the selected options? You know what you are doing?", "wpom" ) . "');\">\n";
				print "<input type=\"hidden\" name=\"wpom_admin_action\" value=\"deletemultiple\"/>\n";
				print "<p><table class=\"widefat post fixed\">\n";
				print "<thead>\n";
				print "<tr><th class=\"manage-column\" width=\"90\" style=\"padding-left:0px\"><input type=\"checkbox\" name=\"checkall\" value=\"{$row->option_id}\" onclick=\"dl=window.document.getElementsByName('wpom_deletelist[]');for(i=0;i<dl.length;i++)dl[i].checked=!dl[i].checked;\"/> " . __( "Option ID", "wpom" ) . "</th><th width=\"200\">" . __( "Option name", "wpom" ) . "</th><th>" . __( "Option value", "wpom" ) . "</th><th width=\"100\">" . __( "Autoloaded?", "wpom" ) . "</th><th width=\"100\">" . __( "Choose action", "wpom" ) . "</th></tr>\n";
				print "</thead>\n";
				print "<tbody>\n";
				foreach( $this -> db -> last_result AS $row )
				{
					if( (!in_array( $row -> option_name, $this -> wp_default_options ) && strpos( $row -> option_name, '_transient_' ) === false) || $_POST[ 'wpom_hide_default_options' ] == 'false' )
					{
						$row -> option_value = wordwrap( $row -> option_value, 25, " ", true );
						$row -> option_value = htmlentities( $row -> option_value );
						if( !in_array( $row -> option_name, $this -> wp_default_options ) && strpos( $row -> option_name, '_transient_' ) === false )
							$popular_plugin = "<i>" . __( "Plugin:", "wpom" ) . " " . $this -> _get_popular_plugin( $row -> option_name ) . "</i>";
						else
							$popular_plugin = "<i>" . __( "WordPress internal", "wpom" ) . "</i>";
						print "<tr id=\"wpom_row_{$row->option_id}\" valign=\"top\" class=\"iedit\"><td><input type=\"checkbox\" name=\"wpom_deletelist[]\" value=\"{$row->option_id}\"/> {$row->option_id}</td><td><b>" . str_ireplace( $term, "<b style=\"color:red\">$term</b>", $row -> option_name ) . "</b><br/>$popular_plugin</td><td><div style=\"padding-right:10px;overflow:hidden;float:left;\" id=\"wpom_value_{$row->option_id}\">" . str_ireplace( $term, "<b>$term</b>", $row -> option_value ) . "</div></td><td>{$row->autoload}</td><td><a href=\"javascript:;\" onclick=\"if(confirm('" . __( "Really delete this option?! You know what you are doing?!", "wpom" ) . "'))jQuery.ajax({type:'POST',url:'" . $_SERVER[ 'REQUEST_URI' ] . "',data:'wpom_admin_action=delete&id={$row->option_id}',success: function(msg){jQuery('#wpom_row_{$row->option_id}').fadeOut('slow')}});\">" . __( "delete", "wpom" ) . "</a> <a href=\"javascript:;\" onclick=\"jQuery.get('" . $_SERVER[ 'REQUEST_URI' ] . "&wpom_admin_action=debug&id={$row->option_id}',function(txt){txtsplit=txt.split('<!-- WPOM AJAX CONTENT -->'); jQuery('#wpom_value_{$row->option_id}').html(txtsplit[1]);});\">" . __( "debug", "wpom" ) . "</a></td></tr>\n";
					}
				}
				print "</tbody>\n";
				print "</table></p>\n";
				print "<p class=\"submit\"><input type=\"submit\" name=\"wpom_submitdeletion\" value=\"" . __( "Delete all selected options", "wpom" ) . "\"/></p>\n";
				print "</form>";
			}
			else	print "<p>" . __( "Sorry, nothing found.", "wpom" ) . " :-(</p>";
		}
		print "</div>\n";
		print "</div>\n";
		print "</div>\n";


		// Autosearch
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<form name=\"wpom_autosearchform\" method=\"POST\" action=\"" . $_SERVER[ "REQUEST_URI" ] . "\" enctype=\"multipart/form-data\">\n";
		print "<input type=\"hidden\" name=\"wpom_admin_action\" value=\"autosearch\"/>\n";
		print "<h3>" . __( "Autosearch deprecated options", "wpom" ) . "</h3>\n";
		print "<div class=\"inside\">\n";
		print "<p>" . __( "What we do here? We will search all plugin files for options used by the plugin and match it against the entrys of the WordPress options database table.", "wpom" ) . "</p>";
		print "<p><br/><b>" . __( "Show options that are unsecure to delete, or be strict?", "wpom" ) . "</b></p>";
		print "<p><select name=\"wpom_show_insecure\">\n";
			print "<option value=\"false\"" . ($_POST[ 'wpom_show_insecure' ] == 'false' ? "selected=\"selected\"" : "") . ">" . __( "No, please be strict!", "wpom" ) . "</option>\n";
			print "<option value=\"true\"" . ($_POST[ 'wpom_show_insecure' ] == 'true' ? "selected=\"selected\"" : "") . ">" . __( "Okay, show me the risky things too!", "wpom" ) . "</option>\n";
		print "</select></p>\n";
		print "<p><br/><b>" . __( "What plugins should be determined?", "wpom" ) . "</b></p>";
		print "<p><select name=\"wpom_determ_plugins\">\n";
			print "<option value=\"active\"" . ($_POST[ 'wpom_determ_plugins' ] == 'active' ? "selected=\"selected\"" : "") . ">" . __( "Only activated plugins", "wpom" ) . "</option>\n";
			print "<option value=\"all\"" . ($_POST[ 'wpom_determ_plugins' ] == 'all' ? "selected=\"selected\"" : "") . ">" . __( "All installed plugins", "wpom" ) . "</option>\n";
		print "</select></p>\n";
		print "<p class=\"submit\"><input type=\"submit\" name=\"wpom_submitsearch\" value=\"" . __( "Match all plugins against options", "wpom" ) . "\"/></p>\n";
		print "</form>\n";
		// Run search
		if( $_POST[ 'wpom_admin_action' ] == 'autosearch' )
		{
			$rowsfound = $this -> db -> query( "SELECT * FROM {$this->tp}options WHERE 1 ORDER BY blog_id ASC, option_name ASC;" );
			if( $rowsfound )
			{
				$plugin_files = array();
				$plugin_options = array();
				$plugin_options_insecure = array();
				// Load all plugin options
				$read_dirs = array();
				if( $_POST[ 'wpom_determ_plugins' ] == 'active' )
				{
					$active_plugins = get_option( 'active_plugins' );
					foreach( $active_plugins AS $ap )
					{
						$ap_arr = explode( "/", $ap );
						if( count( $ap_arr ) > 1 )
							$read_dirs[] = WPOM_PLUGIN_PATH . $ap_arr[ 0 ];
						else
							$read_dirs[] = WPOM_PLUGIN_PATH . $ap;
					}
				}
				else
				{
					if( $d = opendir( WPOM_PLUGIN_PATH ) )
					{
						while( false !== ($sd = readdir( $d )) )
						{
							if( is_dir( WPOM_PLUGIN_PATH . $sd ) && $sd != '.' && $sd != '..' )
								$read_dirs[] = WPOM_PLUGIN_PATH . $sd;
						}
					}
				}
				foreach( $read_dirs AS $dir )
				{
					if( is_dir( $dir ) )
					{
						$d = opendir( $dir );
						while( false !== ($pf = readdir( $d )) )
						{
							if( is_dir( $dir . '/' . $pf ) )
							{
								if( $pf != '.' && $pf != '..' )
									$read_dirs[] = $dir . '/'. $pf;
							}
						}
					}
				}
				// Directorys loaded, now read all files (I did that because I don't want to do it with recursion)
				foreach( $read_dirs AS $dir )
				{
					if( is_dir( $dir ) )
					{
						$d = opendir( $dir );
						while( false !== ($pf = readdir( $d )) )
							$this -> _parse_plugin_file( $plugin_files, $plugin_options, $plugin_options_insecure, $dir, $pf );
					}
					else
					{
						$this -> _parse_plugin_file( $plugin_files, $plugin_options, $plugin_options_insecure, '', $dir );
					}
				}
				$plugin_options = array_unique( $plugin_options );
				$plugin_options_insecure[] = 'rss_';
				$plugin_options_insecure = array_unique( $plugin_options_insecure );
				// Load stored option accesses
				if( file_exists( WPOM_DATAFILE ) )
					$wpomOA = unserialize( file_get_contents( WPOM_DATAFILE ) );
				else
					$wpomOA = array();
				// Output hits not in plugin_options array
				print "<p><strong>" . __( "Number of total options found:", "wpom" ) . " $rowsfound</strong></p>\n";
				$tbl  = "<p><table class=\"widefat post fixed\">\n";
				$tbl .= "<thead>\n";
				$tbl .= "<tr><th class=\"manage-column\" width=\"90\" style=\"padding-left:0px\"><input type=\"checkbox\" name=\"checkall\" value=\"{$row->option_id}\" onclick=\"dl=window.document.getElementsByName('wpom_deletelist[]');for(i=0;i<dl.length;i++)dl[i].checked=!dl[i].checked;\"/> " . __( "Option ID", "wpom" ) . "</th><th width=\"200\">" . __( "Option name", "wpom" ) . "</th><th>" . __( "Option value", "wpom" ) . "</th><th width=\"100\">" . __( "Autoloaded?", "wpom" ) . "</th><th width=\"100\">" . __( "Choose action", "wpom" ) . "</th></tr>\n";
				$tbl .= "</thead>\n";
				$tbl .= "<tbody>\n";
				$count_no_match = 0;
				$count_insecure_match = 0;
				foreach( $this -> db -> last_result AS $row )
				{
					if( !in_array( $row -> option_name, $this -> wp_default_options ) && !in_array( $row -> option_name, $plugin_options ) && strpos( $row -> option_name, '_transient_' ) === false )
					{
						// Check against autocollected accessed options
						if( isset( $wpomOA[ $row -> option_name ] ) )
							$autocollected_warning = "<br/><strong style=\"color:red\">" . __( "Warning! Recognized accessed option!", "wpom" ) . "</strong><br/><span style=\"color:red\">" . __( "Last access:", "wpom" ) . " " . date( "d/m/y H:i", $wpomOA[ $row -> option_name ] ) . "</span>";
						else
							$autocollected_warning = "";
						if( !in_array( $row -> option_name, $this -> wp_default_options ) )
							$popular_plugin = "<i>" . __( "Plugin:", "wpom" ) . " " . $this -> _get_popular_plugin( $row -> option_name ) . "</i>";
						else
							$popular_plugin = "<i>" . __( "WordPress internal", "wpom" ) . "</i>";
						$row -> option_value = wordwrap( $row -> option_value, 25, " ", true );
						$row -> option_value = htmlentities( $row -> option_value );
						if( $row -> option_name == str_ireplace( $plugin_options_insecure, '', $row -> option_name ) )
						{
							$row -> option_name = "<b style=\"color:green\">" . __( "Maybe you can delete this:", "wpom" ) . "</b><br/><a href=\"" . $_SERVER[ "REQUEST_URI" ] . "&wpom_admin_action=pluginsearch&wpom_searchterm={$row->option_name}\">{$row->option_name}</a>";
							$tbl .= "<tr id=\"wpom_row_{$row->option_id}\" valign=\"top\" class=\"iedit\"><td><input type=\"checkbox\" name=\"wpom_deletelist[]\" value=\"{$row->option_id}\"/> {$row->option_id}</td><td><b>{$row->option_name}</b><br/>$popular_plugin$autocollected_warning</td><td><div style=\"padding-right:10px;overflow:hidden;float:left;\" id=\"wpom_value_{$row->option_id}\">{$row->option_value}</div></td><td>{$row->autoload}</td><td><a href=\"javascript:;\" onclick=\"if(confirm('" . __( "Really delete this option?! You know what you are doing?!", "wpom" ) . "'))jQuery.ajax({type:'POST',url:'" . $_SERVER[ 'REQUEST_URI' ] . "',data:'wpom_admin_action=delete&id={$row->option_id}',success: function(msg){jQuery('#wpom_row_{$row->option_id}').fadeOut('slow')}});\">" . __( "delete", "wpom" ) . "</a> <a href=\"javascript:;\" onclick=\"jQuery.get('" . $_SERVER[ 'REQUEST_URI' ] . "&wpom_admin_action=debug&id={$row->option_id}',function(txt){txtsplit=txt.split('<!-- WPOM AJAX CONTENT -->'); jQuery('#wpom_value_{$row->option_id}').html(txtsplit[1]);});\">" . __( "debug", "wpom" ) . "</a></td></tr>\n";
							$count_no_match++;
						}
						else
						if( $_POST[ 'wpom_show_insecure' ] == 'true' )
						{
							$row -> option_name = "<b style=\"color:red\">" . __( "Insecure to delete:", "wpom" ) . "</b><br/><a href=\"" . $_SERVER[ "REQUEST_URI" ] . "&wpom_admin_action=pluginsearch&wpom_searchterm={$row->option_name}\">{$row->option_name}</a>";
							$tbl .= "<tr id=\"wpom_row_{$row->option_id}\" valign=\"top\" class=\"iedit\"><td><input type=\"checkbox\" name=\"wpom_deletelist[]\" value=\"{$row->option_id}\"/> {$row->option_id}</td><td><b>{$row->option_name}</b><br/>$popular_plugin$autocollected_warning</td><td><div style=\"padding-right:10px;overflow:hidden;float:left;\" id=\"wpom_value_{$row->option_id}\">{$row->option_value}</div></td><td>{$row->autoload}</td><td><a href=\"javascript:;\" onclick=\"if(confirm('" . __( "Really delete this option?! You know what you are doing?!", "wpom" ) . "'))jQuery.ajax({type:'POST',url:'" . $_SERVER[ 'REQUEST_URI' ] . "',data:'wpom_admin_action=delete&id={$row->option_id}',success: function(msg){jQuery('#wpom_row_{$row->option_id}').fadeOut('slow')}});\">" . __( "delete", "wpom" ) . "</a> <a href=\"javascript:;\" onclick=\"jQuery.get('" . $_SERVER[ 'REQUEST_URI' ] . "&wpom_admin_action=debug&id={$row->option_id}',function(txt){txtsplit=txt.split('<!-- WPOM AJAX CONTENT -->'); jQuery('#wpom_value_{$row->option_id}').html(txtsplit[1]);});\">" . __( "debug", "wpom" ) . "</a></td></tr>\n";
							$count_insecure_match++;
						}
					}
				}
				$tbl .= "</tbody>\n";
				$tbl .= "</table></p>\n";
				print "<p>";
				print "<b>" . __( "Options with no match in any plugin file:", "wpom" ) . " $count_no_match</b> (" . __( "This does not mean, that there isn't any plugin that uses the options! Check before you delete!", "wpom" ) . ")<br/>";
				if( $_POST[ 'wpom_show_insecure' ] == 'true' )
					print "<b>" . __( "Options with no direct match in any plugin file, but with some similarities:", "wpom" ) . " $count_insecure_match</b> (" . __( "Check before you delete this options!", "wpom" ) . ")<br/>";
				print "</p>";
				print "<form name=\"wpom_deletemultiple2\" method=\"POST\" action=\"" . $_SERVER[ "REQUEST_URI" ] . "\" enctype=\"multipart/form-data\" onsubmit=\"return confirm('" . __( "Are you really sure that you want to delete the selected options? You know what you are doing?", "wpom" ) . "');\">\n";
				print "<input type=\"hidden\" name=\"wpom_admin_action\" value=\"deletemultiple\"/>\n";
				print $tbl;
				print "<p class=\"submit\"><input type=\"submit\" name=\"wpom_submitdeletion\" value=\"" . __( "Delete all selected options", "wpom" ) . "\"/></p>\n";
				print "</form>";
				print "<p>" . __( "Hint: You can click the name of an option and search in all active plugin files for that term to check if that option is found in any plugin source code.", "wpom" ) . "</p>";
			}
			else	print "<p>" . __( "Sorry, nothing found in the options table?", "wpom" ) . " :-(</p>";
		}
		print "</div>\n";
		print "</div>\n";
		print "</div>\n";


		// Search plugin files for a term
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<form name=\"wpom_pluginsearchform\" method=\"POST\" action=\"" . $_SERVER[ "REQUEST_URI" ] . "\" enctype=\"multipart/form-data\">\n";
		print "<input type=\"hidden\" name=\"wpom_admin_action\" value=\"pluginsearch\"/>\n";
		print "<h3>" . __( "Search for term in plugin source files", "wpom" ) . "</h3>\n";
		print "<div class=\"inside\">\n";
		print "<p>" . __( "What we do here? We will search all plugin files for your searchterm. Maybe you can identify some unknown options this way.", "wpom" ) . "</p>";
		print "<p><h4>" . __( "Please enter a search term", "wpom" ) . "</h4></p>\n";
		print "<p><input type=\"text\" name=\"wpom_searchterm\" size=\"20\" maxlength=\"80\" value=\"" . htmlentities( $_REQUEST[ 'wpom_searchterm' ] ) . "\"/></p>\n";
		print "<p><h4>" . __( "What plugins should be determined?", "wpom" ) . "</h4></p>\n";
		print "<p><select name=\"wpom_determ_plugins\">\n";
			print "<option value=\"active\"" . ($_POST[ 'wpom_determ_plugins' ] == 'active' ? "selected=\"selected\"" : "") . ">" . __( "Only activated plugins", "wpom" ) . "</option>\n";
			print "<option value=\"all\"" . ($_POST[ 'wpom_determ_plugins' ] == 'all' ? "selected=\"selected\"" : "") . ">" . __( "All installed plugins", "wpom" ) . "</option>\n";
		print "</select></p>\n";
		print "<p class=\"submit\"><input type=\"submit\" name=\"wpom_submitsearch\" value=\"" . __( "Search plugin source files", "wpom" ) . "\"/></p>\n";
		print "</form>\n";
		// Run search
		if( $_REQUEST[ 'wpom_admin_action' ] == 'pluginsearch' && !empty( $_REQUEST[ 'wpom_searchterm' ] ) )
		{
			$term = $_REQUEST[ 'wpom_searchterm' ];
			$plugin_files = array();
			$results = array();
			// Load all plugin options
			$read_dirs = array();
			if( $_POST[ 'wpom_determ_plugins' ] == 'active' || !isset( $_POST[ 'wpom_determ_plugins' ] ) )
			{
				$active_plugins = get_option( 'active_plugins' );
				foreach( $active_plugins AS $ap )
				{
					$ap_arr = explode( "/", $ap );
					if( count( $ap_arr ) > 1 )
						$read_dirs[] = WPOM_PLUGIN_PATH . $ap_arr[ 0 ];
					else
						$read_dirs[] = WPOM_PLUGIN_PATH . $ap;
				}
			}
			else
			{
				if( $d = opendir( WPOM_PLUGIN_PATH ) )
				{
					while( false !== ($sd = readdir( $d )) )
					{
						if( is_dir( WPOM_PLUGIN_PATH . $sd ) && $sd != '.' && $sd != '..' )
							$read_dirs[] = WPOM_PLUGIN_PATH . $sd;
					}
				}
			}
			foreach( $read_dirs AS $dir )
			{
				if( is_dir( $dir ) )
				{
					$d = opendir( $dir );
					while( false !== ($pf = readdir( $d )) )
					{
						if( is_dir( $dir . '/' . $pf ) )
						{
							if( $pf != '.' && $pf != '..' )
								$read_dirs[] = $dir . '/'. $pf;
						}
					}
				}
			}
			// Directorys loaded, now read all files (I did that because I don't want to do it with recursion)
			foreach( $read_dirs AS $dir )
			{
				if( is_dir( $dir ) )
				{
					$d = opendir( $dir );
					while( false !== ($pf = readdir( $d )) )
						$this -> _search_plugin_file( $term, $plugin_files, $results, $dir, $pf );
				}
				else
				{
					$this -> _search_plugin_file( $term, $plugin_files, $results, '', $dir );
				}
			}
			$rowsfound = count( $results );
			// Output hits in plugin source files
			print "<p><strong>" . __( "Number of total hits found:", "wpom" ) . " $rowsfound " . __( "file(s)", "wpom" ) . "</strong></p>\n";
			if( $rowsfound )
			{
				print "<p><table class=\"widefat post fixed\">\n";
				print "<thead>\n";
				print "<tr><th class=\"manage-column\" width=\"300\">" . __( "Plugin file", "wpom" ) . "</th><th class=\"manage-column\" width=\"130\">" . __( "Number of hits total", "wpom" ) . "</th><th class=\"manage-column\">" . __( "Code snippet of first hit in source file", "wpom" ) . "</th></tr>\n";
				print "</thead>\n";
				print "<tbody>\n";
				foreach( $results AS $file => $hit_arr )
					print "<tr valign=\"top\" class=\"iedit\" onmouseover=\"this.style.backgroundColor='#EEE';\" onmouseout=\"this.style.backgroundColor='';\"><td>" . $hit_arr[ 'source_file_path' ] . "</td><td>" . $hit_arr[ 'hits' ] . "</td><td>" . str_ireplace( $term, "<b style=\"color:red\">$term</b>", $hit_arr[ 'snippet' ] ) . "</td></tr>\n";
				print "</tbody>\n";
				print "</table></p>\n";
			}
			else	print "<p>" . __( "Sorry, nothing found.", "wpom" ) . " :-(</p>";
		}
		print "</div>\n";
		print "</div>\n";
		print "</div>\n";


		// Display collected option accesses
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<h3>" . __( "Automatically collected accessed options", "wpom" ) . "</h3>\n";
		print "<div class=\"inside\" style=\"line-height:160%;font-size:1em;\">\n";
		print "<p>" . __( "Here you see a list of all options your WordPress installation definitly used in the past. The timestamp (MM/DD/YY HH:MM) describes when the option was last accessed.", "wpom" ) . "</p>";
		if( file_exists( WPOM_DATAFILE ) )
		{
			$wpomOA = unserialize( file_get_contents( WPOM_DATAFILE ) );
			ksort( $wpomOA );
			print "<p><table class=\"widefat post fixed\">\n";
			print "<thead>\n";
			print "<tr><th width=\"200\">" . __( "Option name", "wpom" ) . "</th><th width=\"100\">" . __( "Last access", "wpom" ) . "</th></tr>\n";
			print "</thead>\n";
			print "<tbody>\n";
			foreach( $wpomOA AS $option_name => $time )
			{
				// Mark entrys with color
				if( $time < (time() - (86400 * 7) ) )
					$ts_style = "background-color:#FFA";
				else
				if( $time < (time() - (86400 * 14) ) )
					$ts_style = "background-color:#FC0";
				else
				if( $time < (time() - (86400 * 30) ) )
					$ts_style = "background-color:#FCC";
				else
					$ts_style = "background-color:#CFC";
				// Popular plugin
				$popular_plugin = "<i>" . __( "Plugin:", "wpom" ) . " " . $this -> _get_popular_plugin( $option_name ) . "</i>";
				print "<tr class=\"iedit\"><td>$option_name<br/>$popular_plugin</td><td style=\"$ts_style\">" . date( 'm/d/y, H:i', $time ) . "</td></tr>\n";
			}
			print "</tbody>\n";
			print "</table></p>\n";
			print "<p><strong>" . count( $wpomOA ) . " " . __( "Options stored in datafile.", "wpom" ) . "</strong></p>";
			print "<p>" . __( "Legend:", "wpom" ) . " <span style=\"background-color:#CFC\">" . __( "not older than 7 days", "wpom" ) . "</span> <span style=\"background-color:#FFA\">" . __( "older than 7 days", "wpom" ) . "</span> <span style=\"background-color:#FC0\">" . __( "older than 14 days", "wpom" ) . "</span> <span style=\"background-color:#FCC\">" . __( "older than 30 days", "wpom" ) . "</span></p>";
			print "<p>" . __( "Note: Some plugin options can't be collected automatically (see faq).", "wpom" ) . "</p>";
		}
		else	print "<p>" . __( "Oops, no datafile found. Maybe you have disabled this feature.", "wpom" ) . "</p>";
		print "</div>";
		print "</div>\n";
		print "</div>\n";


		// Setup form
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<form name=\"wpom_setupform\" method=\"POST\" action=\"" . $_SERVER[ "REQUEST_URI" ] . "\" enctype=\"multipart/form-data\">\n";
		print "<input type=\"hidden\" name=\"wpom_admin_action\" value=\"setoptions\"/>\n";
		print "<h3>" . __( "Plugin setup", "wpom" ) . "</h3>\n";
		print "<div class=\"inside\">\n";
		print "<h4>" . __( "Automatically collect data about accessed options?", "wpom" ) . "</h4>";
		print "<p><input type=\"checkbox\" name=\"autocollect\" value=\"true\"" . ($this -> wpom_PluginOptions[ 'autocollect' ] == 'true' ? ' checked=\"checked\"' : '') . "/> " . __( "Activate to enable this feature. Deactivate to disable. On deactivation the datafile will be deleted.", "wpom" ) . "</p>";
		print "<p class=\"submit\"><br/><input type=\"submit\" name=\"wpom_submitsetup\" value=\"" . __( "Set options", "wpom" ) . "\"/></p>\n";
		print "</form>\n";
		print "</div>";
		print "</div>\n";
		print "</div>\n";


		// Donate link and support informations
		print "<div id=\"poststuff\">\n";
		print "<div class=\"postbox\">\n";
		print "<h3>" . __( "Donate &amp; support", "wpom" ) . "</h3>\n";
		print "<div class=\"inside\" style=\"line-height:160%;font-size:1em;\">\n";
		print __( "Please", "wpom" ) . " <a href=\"https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=m_schieferdecker%40hotmail%2ecom&item_name=wpom%20wp%20plugin&no_shipping=0&no_note=1&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8\">" . __( "DONATE", "wpom" ) . "</a> " . __( "if you like this plugin.", "wpom" ) . "<br/>";
		print "<br/>" . __( "If you need support, want to report bugs or suggestions, drop me an ", "wpom" ) . " <a href=\"mailto:m_schieferdecker@hotmail.com\">" . __( "email", "wpom" ) . "</a> " . __( "or visit the", "wpom" ) . " <a href=\"http://wordpress.org/extend/plugins/wp-options-manager/\">" . __( "plugin homepage", "wpom" ) . "</a>.<br/>";
		print "<br/>" . __( "Translations: ", "wpom" ) . " Gianni Diurno (Italiano), Marc Schieferdecker (Deutsch)<br/>";
		print "<br/>" . __( "And this persons I thank for a donation:", "wpom" ) . " Michael Mette<br/>";
		print "<br/>" . __( "Final statements: Code is poetry. Motorcycles are cooler than cars.", "wpom" );
		print "</div>";
		print "</div>\n";
		print "</div>\n";


		// Close container
		print "</div>\n";

		// Nice display
		if( version_compare( substr($wp_version, 0, 3), '2.5', '<' ) )
		{
?>
		<script type="text/javascript">
		//<!--
			var wpom_openPanel = <?php print $open; ?>;
			var wpom_PanelCounter = 1;
			jQuery('.postbox h3').prepend('<a class="togbox">+</a> ');
			jQuery('.postbox h3').click( function() { jQuery(jQuery(this).parent().get(0)).toggleClass('closed'); } );
			jQuery('.postbox h3').each(function() {
				if( (wpom_PanelCounter++) != wpom_openPanel )
					jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
			});
		//-->
		</script>
		<style type="text/css">
			h4 {
				margin-bottom:0em;
			}
		</style>
<?php
		}
	}

	function _parse_plugin_file( &$plugin_files, &$plugin_options, &$plugin_options_insecure, $dir, $pf )
	{
		$fSuffix_Arr = explode( ".", $pf );
		$fSuffix = end( $fSuffix_Arr );
		if( strtolower( $fSuffix ) == 'php' )
		{
			if( !empty( $dir ) )
			{
				$plugin_files[] = $dir . '/' . $pf;
				$plugin_source = file_get_contents( $dir . '/' . $pf );
			}
			else
			{
				$plugin_files[] = $pf;
				$plugin_source = file_get_contents( $pf );
			}
			preg_match_all( '/(get_option|update_option|add_option|delete_option)\((.*)(\)|,)/siU', $plugin_source, $matches );
			if( count( $matches[ 0 ] ) )
			{
				foreach( $matches[ 2 ] AS $option )
				{
					$option = str_replace( array( "'", '"', ' ' ), '', $option );
					if( strpos( $option, '$' ) === false && strpos( $option, '.' ) === false )
					{
						$plugin_options[] = $option;
					}
					else
					{
						if( strpos( $option, '.' ) === false )
						{
							$tarr = explode( '->', $option );
							if( count( $tarr ) > 1 )
							{
								$option = trim( end( $tarr ) );
								$vsearch = '/' . preg_quote( $option ) . '.*=.*(\'|")(.*)(\'|");/U';
								preg_match( $vsearch, $plugin_source, $vmatch );
								if( !empty( $vmatch[ 2 ] ) )
									$plugin_options[] = $vmatch[ 2 ];
							}
							else
							{
								$vsearch = '/' . preg_quote( $option ) . '.*=.*(\'|")(.*)(\'|");/U';
								preg_match( $vsearch, $plugin_source, $vmatch );
								if( !empty( $vmatch[ 2 ] ) )
									$plugin_options[] = $vmatch[ 2 ];
							}
						}
						else
						{
							$tarr = explode( '.', $option );
							foreach( $tarr AS $insec_str )
								if( strpos( $insec_str, '(' ) === false && strlen( $insec_str ) > 2 )
									$plugin_options_insecure[] = str_replace( '$', '', $insec_str );
						}
					}
				}
			}
		}
	}

	function _search_plugin_file( $term, &$plugin_files, &$results, $dir, $pf )
	{
		$fSuffix_Arr = explode( ".", $pf );
		$fSuffix = end( $fSuffix_Arr );
		if( strtolower( $fSuffix ) == 'php' )
		{
			if( !empty( $dir ) )
			{
				$source_file_path = $dir . '/' . $pf;
				$plugin_source = file_get_contents( $dir . '/' . $pf );
			}
			else
			{
				$source_file_path = $pf;
				$plugin_source = file_get_contents( $pf );
			}
			$plugin_files[] = $source_file_path;
			// Search for term in source
			preg_match_all( '/(' . preg_quote( $term, '/' ) . ')+/siU', $plugin_source, $matches );
			if( count( $matches[ 0 ] ) )
			{
				$snippet = substr( $plugin_source, ((stripos( $plugin_source, $term ) - 100) <= 0 ? 0 : stripos( $plugin_source, $term ) - 100), ((stripos( $plugin_source, $term ) - 100) <= 0 ? 100 : strlen( $term ) + 200) );
				$results[ $source_file_path ] = array( 'source_file_path' => str_replace( WPOM_PLUGIN_PATH, '', $source_file_path ), 'hits' => count( $matches[ 0 ] ), "snippet" => str_replace( array( "\n", "\t" ), array( "<br/>", "&nbsp;&nbsp;&nbsp;" ), htmlentities( $snippet ) ) );
			}
		}
	}

	function _get_popular_plugin( $option )
	{
		if( str_replace( array_keys( $this -> wp_popular_plugins ), '', $option ) != $option )
		{
			foreach( $this -> wp_popular_plugins AS $okey => $plugin )
			{
				if( strpos( $option, $okey ) !== false )
					return $plugin;
			}
		}
		return __( "unknown", "wpom" );
	}

	/*
	 * Save/Restore plugin configuration functions
	 */
	function _get_options()
	{
		$_wpom_PluginOptions = get_option( $this -> wpom_PluginOptionsName );
		// Overwrite defaults
		if( is_array( $_wpom_PluginOptions ) && count( $_wpom_PluginOptions ) )
		{
			foreach( $_wpom_PluginOptions AS $oKey => $oVal )
				$this -> wpom_PluginOptions[ $oKey ] = $oVal;
		}
		// Set default options to wp db if no existing options or new options are found
		if( !count( $_wpom_PluginOptions ) || count( $_wpom_PluginOptions ) != count( $this -> wpom_PluginOptions ) )
			update_option( $this -> wpom_PluginOptionsName, $this -> wpom_PluginOptions );
	}
	function _set_options()
	{
		update_option( $this -> wpom_PluginOptionsName, $this -> wpom_PluginOptions );
	}

	/*
	 * Functions for making this plugin the first in the plugin load list
	 * I have to add my filter first to recognize wich options used by the plugins below me!
	 * I know I'm a pervert, but it does the job!
	 */
	function _make_me_first_in_plugin_load_list()
	{
		$active_plugins = get_option( 'active_plugins' );
		if( is_array( $active_plugins ) )
		{
			if( $active_plugins[ 0 ] != 'wp-options-manager/wp_options_manager.php' )
			{
				$active_plugins_new = array();
				$active_plugins_new[] = 'wp-options-manager/wp_options_manager.php';
				foreach( $active_plugins AS $plugin )
				{
					if( $plugin != 'wp-options-manager/wp_options_manager.php' )
						$active_plugins_new[] = $plugin;
				}
				remove_action( 'update_option_active_plugins', array( $this, '_make_me_first_in_plugin_load_list' ) );
				update_option( 'active_plugins', $active_plugins_new );
			}
		}
	}

	// On deactivation kill datafile and stop making me first in the plugin load list
	function _deactivate_plugin()
	{
		// Remove action to make this plugin first in the load list
		remove_action( 'update_option_active_plugins', array( $this, '_make_me_first_in_plugin_load_list' ) );
		delete_option( $this -> wpom_PluginOptionsName );
		// Delete datafile
		if( file_exists( WPOM_DATAFILE ) )
			unlink( WPOM_DATAFILE );
	}
}

// Create class instance and load options
$wpom_Plugin = new wp_options_manager();
$wpom_Plugin -> _get_options();

// Admin page wrapper
function wrapper_wpom_AdminPage()
{
	global $wpom_Plugin;
	add_options_page( 'WP Options Manager', 'WP Options Manager', 9, basename(__FILE__), array( &$wpom_Plugin, 'wpom_AdminPage' ) );
}
// Add action: Admin page
add_action( 'admin_menu', 'wrapper_wpom_AdminPage', 0 );

// Actions for making this plugin first in the plugin load list (option: active_plugins)
register_activation_hook( __FILE__, array( $wpom_Plugin, '_make_me_first_in_plugin_load_list' ) );
add_action( 'update_option_active_plugins', array( $wpom_Plugin, '_make_me_first_in_plugin_load_list' ) );

// Action for deactivation
register_deactivation_hook( __FILE__, array( $wpom_Plugin, '_deactivate_plugin' ) );

/*
 * Class: Filter function for get_option, store accessed options in global array
 */
$wpomOptionsAccess = array();
class wpom_filter_options
{
	var $option_name;
	var $wpomOptionsAccess;

	function __construct( $option_name, &$wpomOptionsAccess )
	{
		$this -> option_name = $option_name;
		$this -> wpomOptionsAccess =& $wpomOptionsAccess;
	}
	function wpom_filter_options( $option_name ) { $this -> __construct( $option_name ); }

	function wpom_store_access_option_var( $arg )
	{
		remove_filter( 'pre_option_' . $this -> option_name, array( $this, 'wpom_store_access_option_var' ) );
		$this -> wpomOptionsAccess[ $this -> option_name ] = time();
		return false;
	}
}

// Add filters for all options if configured
$wpomFiltersAdded = array();
function wpom_add_get_option_filter()
{
	global $wpdb, $table_prefix, $wpom_Plugin, $wpomOptionsAccess, $wpomFiltersAdded;
	$rowsfound = $wpdb -> query( "SELECT option_name FROM $table_prefix"."options WHERE 1;" );
	if( $rowsfound )
	{
		foreach( $wpdb -> last_result AS $row )
		{
			if( !empty( $row -> option_name ) )
			{
				if( !in_array( $row -> option_name, $wpomFiltersAdded ) && !in_array( $row -> option_name, $wpom_Plugin -> wp_default_options ) && strpos( $row -> option_name, 'rss_' ) !== 0  && strpos( $row -> option_name, '_transient_' ) === false )
				{
					add_filter( 'pre_option_' . $row -> option_name, array( new wpom_filter_options( $row -> option_name, $wpomOptionsAccess ), 'wpom_store_access_option_var' ) );
					$wpomFiltersAdded[] = $row -> option_name;
				}
			}
		}
	}
}
if( $wpom_Plugin -> wpom_PluginOptions[ 'autocollect' ] == 'true' )
	wpom_add_get_option_filter();

// Save collected data if configured
function wpom_save_options_access()
{
	global $wpomOptionsAccess;
	// Load existing data5
	if( file_exists( WPOM_DATAFILE ) )
		$_wpomOptionsAccess = unserialize( file_get_contents( WPOM_DATAFILE ) );
	else
		$_wpomOptionsAccess = array();
	// Merge data and store
	foreach( $wpomOptionsAccess AS $option_name => $time )
		$_wpomOptionsAccess[ $option_name ] = $time;
	if( (file_exists( WPOM_DATAFILE ) && is_writable( WPOM_DATAFILE )) || (!file_exists( WPOM_DATAFILE ) && is_writable( WPOM_DATADIR )) )
		file_put_contents( WPOM_DATAFILE, serialize( $_wpomOptionsAccess ) );
	else
		echo '<!-- WPOM: NOT WRITEABLE DATAFILE ' . WPOM_DATAFILE . ' -->';
}
if( $wpom_Plugin -> wpom_PluginOptions[ 'autocollect' ] == 'true' )
{
	add_action( 'wp_footer', 'wpom_save_options_access', 10000 );
	add_action( 'admin_footer', 'wpom_save_options_access', 10000 );
}

?>