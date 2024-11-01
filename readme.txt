=== WP-Options-Manager ===
Contributors: Marc Schieferdecker
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=m_schieferdecker%40hotmail%2ecom&item_name=article2pdf%20wp%20plugin&no_shipping=0&no_note=1&tax=0&currency_code=EUR&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: options, admin, manage, plugins, database, wp_options, development
Requires at least: 2.8
Tested up to: 2.8
Stable tag: 0.06

This plugin let you browse, delete and debug the entrys of your WP options table. A nice way to remove deprecated options from uninstalled plugins.

== Description ==

**WP MU USERS:** If you use WP MU please NEVER install this plugin, or your WordPress MU will crash!!!

With this plugin you can browse, delete and debug the entrys of your WordPress options database table. WordPress default options can be hidden automatically and some popular plugin options are referenced with the plugin name. An automatic search for deprecated options is also available and you can automatically let collect which options are accessed. But not enough: Search all activated or installed plugin files by a term to ensure that you only delete options that are not required anymore. This plugin is very useful if you want to remove deprecated options from uninstalled plugins bloating your WP Options database table.

**Important: Only the administrator user should have access to this plugin and should know what he is doing! MAKE BACKUPS OF YOUR DATABASE BEFORE DELETING ANY OPTIONS!**

Languages: English, Deutsch, Italiano.

Please visit my other plugin [article2pdf](http://wordpress.org/extend/plugins/article2pdf/) if you are interested in making your posts and pages available as pdf documents.

== Installation ==

Just install the plugin and activate it. Open the WordPress configuration menu and select the "WP Options Manager" from the menu. Have fun!

== Frequently Asked Questions ==

= I can't activate the plugin in WordPress? =

For sure: You use PHP 4. Please upgrade to PHP 5. PHP 4 is no longer supported. ;)

= Is it risky to delete options from the database table? =

For sure! So please only delete options if you are really really sure that the options are no longer needed by WordPress or the sky will fall on your head. ;)

= Why should I keep my options database table clean? =

The less data, the less the load of your WordPress installation.

= What does debug mean? =

Most of the WordPress options were saved serialized (read: http://php.net/serialize). The debug link simply uses php to unserialize the option value and display it in a readable format.

= Does the debugger translate timestamps in a readable format? =

Yes, the debugger recognizes timestamps and translates them to a readable format.

= Does the debugger translate strings? =

No. A string is a string dude.

= How does the automatic search for deprecated options work? =

The plugin reads all (activated or installed, just as you like) php files of your plugins by searching for the functions "`get_option`" and "`update_option`". After that the wp-options-manager trys to extract the names of the options. Then the entrys of the database table is loaded and the found options are matched against the options from your plugins. But: That doesn't work by 100%! Don't delete if you are not sure if you need this option for your WordPress! Use the plugin source code search tool to verify if an option is found in any source file to verify.

= Why shows the automatic search me surely needed options as "Maybe you can delete"? =

Well, the search for the options in the plugin files is a bit complicated. If the developer simply writes "`get_option('my_plugin_option')`" it's very easy. But sometimes they put their option names into variables or defines and use this as parameter e.g. "`get_option($MyPluginOptionName)`". This is very heave to parse, believe me. If someone has a better idea or can improve the existing code, please drop me an email.

= What is the "search for a term in plugin source files"? =

With this feature you can search for a string in all activated or installed (your choice) plugin files. If any match is found the tool will display the filename in which the term is found and how many matches we have. To verify the match a code snippet is printed. If there are any hits to an option you don't know please don't delete that option. In most of the cases the option is required by the plugin if it's found in a source file.

= After activating the plugin my WordPress get's terrible slow, why? =

By default the WP Options Manager collects informations which options are accessed by WordPress and the activated plugins. That means that I had to filter every option that is accessed. That slows your WordPress a bit down but on a normal machine it should be no problem. If your host is to slow turn the automatic collection of accessed options feature off. That will solve the problem.

= What does the colors of the list under "Automatically collected accessed options" mean? =

If the color is green, the option was accessed in the last 7 days. If the color is yellow the last access is more than 7 days ago. Orange indicates that the access is more than 14 days and red more than 30 days ago. Important: If an option is red it does NOT mean that the option is not needed by a plugin. See next paragraph!

= Is it safe to delete red marked options that are listed under "Automatically collected accessed options"? =

No they are not safe to delete. Why? Some options are only accessed when you change the configuration of a plugin. Maybe you'd changed a plugin option before 6 month, the option will be shown red, but for sure this option is needed. Only delete if you are sure that the plugin that uses the option is not activated or deinstalled. You'll find further informations on that in the next paragraph.

= Why are not all options recognized by the automatically collect accessed options feature? =

It's a programming problem. I made it to make my plugin first in the plugin load list and when my plugin loads I generate filters for the WordPress function "`get_option`" which loads the content of an option. With my filters it's possible to store wich options were accessed and when they were accessed. But: Some options weren't loaded when the plugin loads. They were loaded when the user clicks on the configuration page of the admin panel (example). My hint: Click around your admin panel and through some sites of your blog. The WP Options Manager will recognize the used options and store the access.

= What does the method "wp_options_manager::_make_me_first_in_plugin_load_list()" do? =

Because I have to add my filters for the "`get_option`" function of WordPress first I must be sure that the WP Options Plugin is on FIRST position in the plugin load list. The problem is that WordPress loads the plugins alphabetically - maybe that is not a good idea? ;) - so I had to rewrite the value of the WP option "`active_plugins`". The method rearranges the order of the array containing the active plugins.

= Which PHP Version is required? =

You'll need PHP 5 or above. Maybe if there are many users with an old PHP 4, I can downgrade the plugin to PHP 4. Or - if you are a smart guy - you'll replace the function "`str_ireplace`" by own code ;). But since PHP 4 is not longer developed you should upgrade to PHP 5 soon. PHP 5 is much comfortable.

= Are there any configuration options? =

Yes, the options are filed under the name "`wpom_setup_information`". Actually you only can turn off the automatic collection of accessed options feature.

= Are you gonna improve the plugin? =

Yes and I did that multiple times in the past. Please report your ideas to me and help to improve this plugin.

= Why is your english so terrible? =

I'm from germany, sorry for my bad english.

== Screenshots ==

1. This screenshot shows the formular used to search for option entrys.
2. This screeny is showing the result of an automatic search for deprecated options.
3. This is the formular that help to search all plugin source code by a term.
4. This screen shows the output of the automatically collected accessed options.

== Warranty ==

The plugin does not delete any options without a user clicking on delete and confirming the deletion. If you blow up your WordPress installation: Sorry, not my fault, no warranty for this plugin.

I have to repeat it again: Only delete options if you **really know** what you are doing! If there is any *maybe* do not delete or that option and maybe your WordPress ist lost in space.

Hint: Backup your database then delete then test and restore the backup if something goes wrong.