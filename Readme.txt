/// Installer 09 V2 Memcached
/// All Credit goes to the original code creators, especially to any author for the mods i selected for this. 
/// The original coders of torrentbits and especially to CoLdFuSiOn for carrying on the legacy with Tbdev.
/// All other mods and snippets for this version from CoLdFuSiOn, putyn, pdq, Retro, ezero, Alex2005, system, sir_Snugglebunny, laffin, Wilba, Traffic, dokty, djlee, neptune, scars , Raw, soft, jaits, Melvinmeow, freakynutz  Theres to many to mention here but the upmost respect and credit to you all.
/// Credit's to pdq/putyn for improvements in key areas on the code. Your input has been first class.
/// Credit's to Kidvision for all design and templates used in the Installer projects.

Set Up Instructions :
First extract pic.rar - javairc.rar and GeoIp.rar and ensure there not inside an extra folder from being archived then upload the code to your server and chmod - root - avatars - backup - bitbucket - cache and all nested files and folders - dictbreaker - dir_list - uploads - uploadsubs - imdb imdb/cache imdb/images - include - include/settings settings.txt  install/extra/config.sample.php install/extra/announce.sample.php - logs - torrents.
Create a new database user and password via phpmyadmin - Point to http://yoursite.com/install/index.php - fill in all the required data - log in - Create a new user on entry named System ensure its userid2 so you dont need to alter the autoshout function on include/user_functions.php.
Add your staff ids to config.php first.
Add your staffnames to config.php to access staffpanel.
Installer is set up for linux if on windows then edit install/functions/database.php - its commented
tcl files are included - include each one one your eggdrop.conf
