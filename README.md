YiiPass
===============

Collaborative passwords manager in web browser. Based on the [Yii2 PHP framework](http://www.yiiframework.com/).
Ideal for intranet usage for teams and fast and handy access from the internet. It provides the following features:

* Account credential management via web browser.
* Assign account credentials to various users by Yii2 auth manager component.
* Copy username or password fast into clipboard.
* Responsive user interface by bootstrap frontend framework.
* Import and export functionality via open KeePass XML format. 
* Many apps and desktop programs for all operating systems are using it:
    * [KeePassX (desktop)](https://www.keepassx.org/)
    * [KeePass (desktop)](http://keepass.info/)
    * [KeePassDroid (app - Android)](https://play.google.com/store/apps/details?id=com.android.keepass&hl=en)
    * [MiniKeePass (app - iOS)](https://itunes.apple.com/en/app/minikeepass-secure-password/id451661808?mt=8)
* Last but not least: easy and fast to modify and extend, because it's based on the excellent Yii2 PHP framework.
    * Quick start with coding via the great [online guide](http://www.yiiframework.com/doc-2.0/guide-index.html).

Getting Started
------------------

YiiPass uses SQlite database by default. That allows you to quickly check the YiiPass application without configuration
hassle. The installation works via the [Composer php package manager](https://getcomposer.org/), so you can test YiiPass very quick.

If you're already using Composer, you need only 1 command to install YiiPass:

```
composer --stability=dev --keep-vcs create-project jepster/yiipass yiipass-dev
```

Please let your webserver point to the "web"-directory which will be created afterwards. Like it's default in Yii2
framework. 

All set!
-----------------------

Then you can quickly start using YiiPass via accessing it from the web browser. The following screenshots
will show you how to use YiiPass.

###Search fast
![Search fast](http://preview.intellipass.it/github-images/search.png)

###Copy fast
![Copy fast](http://preview.intellipass.it/github-images/copy-fast.png)

###Integrate with open KeePass format
![KeePass](http://preview.intellipass.it/github-images/keepass.png)

###User permissions
![Permissions](http://preview.intellipass.it/github-images/permissions.png)

###Source code project structure
![Folder structure](http://preview.intellipass.it/github-images/folder-structure.png)

Known issues
-----------------------

YiiPass is still in development. Please mention the following issues. Help would be appreciated. Please consider
the issues management functionality here on GitHub.
* The passwords are saved plain in the database. For higher security, there should be implemented encryption. There's
already a concept (also as graphic). Please ask me (jepster) if you want to learn more.
* Users grid table cannot be sized to mobile size in web frontend.
* Last access is not beeing updated.
* Copy username and password buttons to clipboard are only working if the browser has flash. There's also a concept for
handy copy-function in web browsers without flash.
* If you edit an user account, the user password needs to be inserted.
* There should be a second password form field on the account credentials edit page. That way there would be more
control about the password validity.
