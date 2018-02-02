Mibew Messenger
Copyright 2005-2018 the original author or authors.

REQUIREMENTS

 * Apache web server 1.3.34 or above with the ability to use local .htaccess
   files (mod_rewrite module is optional, but recommended)
 * MySQL database 5.0 or above
 * PHP 5.4 or above with PDO, pdo_mysql, cURL, mbstring and gd extensions

INSTALLATION

1. Create folder with name 'mibew' in the root of your website.
2. Upload all the files contained in this archive (retaining the directory
   structure) into created folder.
   Be sure to chmod the mibew folder to 0755.
3. Add a MySQL database with the name 'mibew'
4. Copy /mibew/configs/default_config.yml to /mibew/configs/config.yml
5. Edit /mibew/configs/config.yml to the information needed to connect to the database
6. Using your web browser visit http://<yourdomain>/mibew/install and
   perform step-by-step installation.
7. Remove /mibew/install.php file from your server
8. Logon as
                  user: admin
                  password: <your password>
9. Get button code and setup it on your site.
10. Configure periodically running tasks by setting up an automated
    process to visit the page http://<yourdomain>/cron?cron_key=<key>

    The full URL including the secret "cron key" used to protect against
    unauthorized access can be seen on the 'General' tab at the 'Settings' page.
11. Change your name.
12. Wait for your visitors on 'Pending users' page.

On unix/linux platforms change the owner of /mibew/files/avatar and
/mibew/cache folders to the user, under which the web server is running
(for instance, www). The owner should have all rights on the folders
/mibew/files/avatar and /mibew/cache
(chmod 0700 /mibew/files/avatar && chmod 0700 /mibew/cache).

UPDATE

1. Backup your actual installation (i.e. code and database).
2. Disable all plugins.
3. Delete all items in your Mibew Messenger directory on the server.
4. Unpack the archive with the official distrubition in that directory.
5. Remove install.php file.
6. Restore configuration (configs/ directory), plugins (plugins/ directory),
   (maybe) custom styles (if you have any), (maybe) additional
   locales (if you use any), and avatars (files/avatar/ directory) from the
   backup you've made at the step 1.
7. Visit http://<yourdomain>/<path to your Mibew Messenger>/update and follow
   the instructions to update the database tables (if needed).
8. Enable disabled plugins.


