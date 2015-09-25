YiiPass
===============

Collaborative passwords manager in web browser. Based on the [Yii2 framework](http://www.yiiframework.com/). It provides the following features:

* Account credential management via web browser.
* Assign account credentials to various users by Yii2 auth manager component.
* Copy username or password fast into clipboard.
* Responsive user interface by bootstrap frontend framework.

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

![Search fast](http://preview.intellipass.it/github-images/search.png)

![Copy fast](http://preview.intellipass.it/github-images/copy-fast.png)

![KeePass](http://preview.intellipass.it/github-images/keepass.png)

![Permissions](http://preview.intellipass.it/github-images/permissions.png)

![Folder structure](http://preview.intellipass.it/github-images/folder-structure.png)
