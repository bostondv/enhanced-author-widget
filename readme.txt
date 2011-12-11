=== Enhanced Author Widget ===
Tags: widget, author, bio, user, gravatar
Requires at least: 2.8
Tested up to: 3.2.1
Stable tag: 0.1

Display the Biographical Info, Gravatar, and Link of any author's profile in your blog's sidebar.

== Description ==

This widget allows you to display the Biographical Info, Gravatar and Link of any author's profile in the sidebar. Subscribers are excluded for obvious reasons (Contributors, Authors, Editors, and Administrators are included).

Please submit any bug reports, support questions, or requests <a href="http://bostondv.com">here</a>.

== Installation ==

1. Upload `ehanced-author-widget` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins -> Installed' menu in your dashboard.
1. Add the 'Enhanced Author' Widget to your sidebar via 'Appearance -> Widgets' in your dashboard.

== Frequently Asked Questions ==

= How can I style the Gravatar image? =

You can add and customize the following CSS within your theme's primary stylesheet/CSS file (normally style.css):

.author-grav img {
	/* These are just some example properties you can use */
	border: 2px solid #eeeeee;
	padding: 3px;
}

= Are subscribers included? =

No. Only contributors, authors, editors, and administrators (i.e. any user able to actually create posts).

= What's a Gravatar? =

A Gravatar is a Globally Recognized Avatar (i.e. your user picture/icon). You can upload one via <a href="http://gravatar.com">http://gravatar.com</a> (it will be attached to your e-mail address).

= Can I only display my Gravatar with the widget? =

Yes; simply leave your "Biographical Info" section (in your user profile) blank, and configure the widget to display your Gravatar.

= If an author doesn't have a Gravatar uploaded, and they configure the widget to display a Gravatar, which image is used? =

The default image is determined by the 'Default Avatar' setting found in your dashboard under Settings -> Discussion.

= I have a Gravatar configured, but it's not appearing. What's up? =

For the Gravatar to appear, you must have the 'Show Avatars' option under Settings -> Discussion set to 'Show Avatars'. Also, please check your 'Maximum Rating' setting and compare it with your own Gravatar's rating.

= What is the 'Custom Title'? =

By default the widget title will be the 'Display Name' of the author but you can override this by entering a value in this field.

= What is the 'Custom Link'? =

By default, if the author's profile has a website a link will be displayed. You can override this link to a custom url with this field.

= What is the 'Custom Author'? =

By default the current post author will be displayed but you can override this by selecting an author from this drop down menu.

== Changelog ==

= 0.1 =
* Initial commit