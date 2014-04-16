sec-scanner
===========

## Security Code Scanner
Security Code Scanner is an application for help developers to make a security code review.<br>
You have to upload your project archive ( zip / tar / rar ), scanner (based on regular expression) try to find potential vulnerabilities. After you will get good look report of all founded potential vulnerabilities ;-)<br><br>
**REMEMBER!** Scanner is as good as the good are your regular expression. So don't use only my regexp, try to make your own. Good tool: [https://www.debuggex.com/](https://www.debuggex.com/)
If you want, you can send to me your regexp and I will put it into repository
# Requirements
1. PHP CLI
2. Rar for PHP: [http://pecl.php.net/package/rar](http://pecl.php.net/package/rar)
3. MySQL
4. Some space for your projects

# Installation
1. Copy config/config.default.php to config/config.php ( config file is not in git repository )
2. Modify config/config.php
3. Execute sec-scanner.sql in mysql client
4. Config your webserver ( hosts, vhosts )

# How to use?
1. Add new project
2. Upload project ( tar / rar / zip )
3. Go to main page and click onto your project page
4. Start scan
5. If something found go to report list


If you have some troubles feel free to ask ;-)