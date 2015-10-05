YiiPass
===============

Collaborative passwords manager in web browser. Based on the [Yii2 PHP framework](http://www.yiiframework.com/).
Ideal for intranet usage for teams. Fast and handy access from the internet. It provides the following features:

* Account credential and user management via web browser.
* Assign account credentials to various users.
* Passwords are saved as encrypted hash in the database. You can specify an unique key for password encryption in
your configuration (config/params.php).
* Copy username or password fast into clipboard.
* Responsive user interface by bootstrap frontend framework.
* Import and export functionality via open KeePass XML format. Many apps and desktop programs for all operating
systems are using the KeePass XML format. For example:
    * [KeePassX (desktop)](https://www.keepassx.org/)
    * [KeePass (desktop)](http://keepass.info/)
    * [KeePassDroid (app - Android)](https://play.google.com/store/apps/details?id=com.android.keepass&hl=en)
    * [MiniKeePass (app - iOS)](https://itunes.apple.com/en/app/minikeepass-secure-password/id451661808?mt=8)
* Last but not least: easy and fast to modify and extend, because it's based on the excellent Yii2 PHP framework.
    * Quick start with coding via the great [online guide](http://www.yiiframework.com/doc-2.0/guide-index.html).

Getting Started
------------------

YiiPass uses SQlite database by default. That allows you to quickly check the YiiPass application without configuration
hassle. The installation works via the [Composer PHP package manager](https://getcomposer.org/), so you can test
YiiPass very quick.

If you're already using Composer, you need only _1 command to install_ YiiPass:

```
composer --stability=dev --keep-vcs create-project jepster/yiipass yiipass-dev
```

Please let your web server point to the "web"-directory which will be created afterwards. Like it's default in Yii2
framework. 

The user for start is:
* username: admin
* password: admin

All set!
-----------------------

Then you can quickly start using YiiPass via accessing it from the web browser. The following screenshots
will show you how to use YiiPass.

###Search fast
![Search fast](http://preview.intellipass.it/github-images/search.png)

###Copy, edit, delete and view fast
![Copy fast](http://preview.intellipass.it/github-images/copy-fast2.png)

###Integrate with open KeePass format
![KeePass](http://preview.intellipass.it/github-images/keepass.png)

###User permissions
![Permissions](http://preview.intellipass.it/github-images/permissions.png)

###Source code project structure
Read more about the application structure in the [Yii2 PHP framework guide](http://www.yiiframework.com/doc-2.0/guide-start-workflow.html#application-structure).
![Folder structure](http://preview.intellipass.it/github-images/folder-structure.png)

Use MySQL, MariaDB, PostgreSQL, CUBRID, Oracle or MSSQL instead of SQlite
-----------------------

Change the configuration to the desired DBMS in the config file (config/db.php). An instruction can be found at 
[bsourcecode.com](http://www.bsourcecode.com/yiiframework2/yii2-0-database-connection/). After the database config is
set, you need to apply the database migrations via the command line program from Yii2 ([more about the command line
program in Yii2](http://www.yiiframework.com/doc-2.0/guide-tutorial-console.html)).
```
yii migrate --migrationPath=@yii/rbac/migrations
yii migrate --migrationPath=modules/yiipass/migrations
```
Now your database is feed with the schema.

The SQlite database, which is used per default, is located in the application root folder. The filename is yiipass.sqlite.

Troubleshooting
-----------------------

### PDO_SQLITE driver not present on Ubuntu Linux server.. what to do?

Then try the following commands

> sudo apt-get update
>
> sudo apt-get install php5-sqlite --fix-missing
>
> service apache2 restart

The first one updates the package manager sources. The second one installs the PHP5 SQLite extension and fixes missing
package sources. The third one restarts the Apache2 web server. If you're using the Nginx web server, the
[Nginx Beginner's Guide](http://nginx.org/en/docs/beginners_guide.html) could help you out.

### Mcrypt extension is missing on Ubuntu Linux server

Then run the following commands.

> sudo apt-get install php5-mcrypt
>
> php5enmod mcrypt
>
> service apache2 restart

The first command installs the PHP5 mcrypt extension. The second one enabled the extension in PHP. The third restarts
the Apache2 webserver. If you use Nginx f.e., the first both commands could help you also.

### Data cannot be written into database 

The there're errors like follows:


> SQLSTATE[HY000]: General error: 8 attempt to write a readonly database
>
> or
>
> SQLSTATE[HY000]: General error: 14 unable to open database file

Then make sure that the folder where the _yiipass.sqlite_ SQlite database resides, has the correct permissions. Also
the SQLite database itself must have the correct permissions. By default, this file is located in the application
root-folder. The folder and the file must be owned by www-data, if you use apache. 
[Further information](http://www.dragonbe.com/2014/01/pdo-sqlite-error-unable-to-open.html)


Known issues
-----------------------

YiiPass is still in development. Please mind the following issues. Help would be appreciated. Please consider
the issues management functionality here on GitHub. Please ask me (jepster) if you want to learn more about the
development.
* Users grid table cannot be sized to mobile size in web frontend.
* Last access field in account credential is not beeing updated.
* Copy username and password buttons to clipboard are only working if the browser has flash. There's also a concept for
handy copy-function in web browsers without flash.
* If you edit an user account, the user password needs to be inserted.
* There should be a second password form field on the account credentials edit page. That way there would be more
control about the password validity.

Bugs, questions, suggestions?
-----------------------

Please feel free to contact me via GitHub. Don't forget the issues management functionality here on GitHub.

![Problem](http://preview.intellipass.it/github-images/problem.jpg)

