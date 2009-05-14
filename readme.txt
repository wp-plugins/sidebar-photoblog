=== Sidebar Photoblog ===
Contributors: Hassan Jahangiry
Donate link: 
Tags: photo,photoblog,image,images,widget,sidebar,wpmu,media,upload,picture,pictures,widget,share,gallery
Requires at least: 2.5
Tested up to: 2.7.1
Stable tag: 2.7

A simple but reliable photoblog plugin for WordPress helps you to share your daily photos on your sidebar easily. 

== Description ==

There are several photoblog plugins for WordPress around the Internet. Most of them, suppose that you are a professional photographer who make much money via his/her camera, but We don't think so!

Sidebar Photoblog doesn't use lots of server resources and hasn't many confusing options. It uses WordPress functions to get maximum flexibility, simplicity  and compatibility .

It also has some nice effects and an archive page for your photos.

To see some screenshots and learn how to add your photos please see [plugin URL](http://wordpresswave.com/plugins/).

 
== Screenshots ==

Screenshots here: http://wordpresswave.com/plugins/sidebar-photoblog/#screenshot


== Frequently Asked Questions ==

= How can I use it? =

After adding widget to your sidebar, the plugin automatically creates a category called 'Sidebar Photoblog' and a page as photo archive:'Browse Photos'. You can edit that category and page but "[sphoto_archive]" should remain in the page content(usually not required).

To insert your photos:

1) Click for new post and choose photoblog category (usally 'Sidebar Photoblog').
2) Upload your photo via WordPress Editor (don't use 'From URL' tab).
3) Insert full size image into post and publish it.

That's all! 

It's completely flexible you can upload several photos or add your own content.

= My photos show in main column of the blog? =

Please remove Recent Posts Widget from your sidebar. This problem will be solved in next version.

= Any other thing? =

-You can customize padding, borders,etc using CSS (Don't forget to disable style related options in widget settings first).
-If your theme can't show large photos properly change 'Max Width' in Settings->Media. You can also change thumbnail size and size of preview pop-up image('Medium size') over there.
  
=Would you like to show your photos in main page?=
Open sidebar-photoblog.php with a text-editor(like notepad) search for "exclude_from_home" and change it to false.

== Installation ==

While doing the installation procedure, it is recommended to go through all the steps first before viewing the output. If you don't, you'll get nasty error messages.


1. Upload `sidebar-photoblog.php` to the `/wp-content/plugins` directory
2. Activate the plugin through the 'Plugins' menu in WordPress backend
3. Go to 'Appearance' menu, then to 'Widgets' (or 'Design' then 'Widgets' for older versions).
4. Add Sidebar Photoblog widget to your sidebar.
5. Save your changes.


= Upgrading =

- Disable the Sidebar Photoblog plugin
- Delete sidebar-photoblog.php from your server
- Download and unzip the new file(s) into the plugins dir
- Enable the Sidebar Photoblog plugin