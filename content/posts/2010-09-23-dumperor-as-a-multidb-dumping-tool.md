---
layout: post
title: ! 'Dumperor: Multi-Database Dumping Toolkit'
slug: dumperor as a multidb dumping tool
date: 2010-09-23 00:00:00
tags:
- Database
- Dumperor
- PHP
- SQL
- 编程
status: publish
comments: true
meta:
  _edit_last: '1'
  views: '904'
  _wp_old_slug: ''
  _oembed_bfc07f6d9591d4b2c40694374c1b7af3: ! '{{unknown}}'
  aktt_notify_twitter: 'no'
---
<div class="illustration_left">
    <a href="http://picasaweb.google.com/lh/photo/lR2jgtHibgYwfhCy3fJyMQ?feat=embedwebsite"><img src="http://lh3.ggpht.com/_ceUJ_lBTHzc/TI44ZufyOhI/AAAAAAAABeM/6ElHu0gdYzY/s800/dumperor.png" /></a>
</div>

<strong>Dumperor</strong> is a multi-database dumping toolkit. It dumps table structures and data from databases, and generates CREATE-TABLE SQL statements for table structures or INSERT SQL statements for data.

Part of the original intention for developing Dumperor is to check whether a migration of SQL scripts from one database to another is successful, you know, by comparing the differences between two files, one dumped before executing scripts and the other after. The second part is to ensure that upgrades to table structures or data not miss anything, similarly. The last part is to take samples of databases and put up development or testing environments with them, or even replace sensitive information with fake data.

Dumperor is written in PHP 5 and hosted on GitHub with the New BSD License:

https://github.com/xbot/Dumperor

For the shortage of time, there must be some limitations and bugs in Dumperor. So reports from users are welcome, emails prefered. I will be very grateful if someone send their suggestions on Dumpeor to me.

<h2>Features</h2>

<ol>
    <li>Currently support Microsoft SQL Server, Oracle and MySQL.</li>
    <li>Dump table structures and generate CREATE-TABLE SQL statements.</li>
    <li>Dump data and generate INSERT SQL statements.</li>
    <li>Dump table structures or data and output them in a human readable format.</li>
    <li>Save dumped information to user-specified files.</li>
    <li>Display table structures with web page tables in the web browser.</li>
    <li>Options controlling which information should be displayed in the web browser.</li>
    <li>Options controlling which information should be and should not be dumped from the database.</li>
    <li>A limit number can be set to take sample of a huge database.</li>
    <li>Needless tables or columns can be prevented from appearing in dumped results.</li>
    <li>Only the needed tables are to be exported if you like.</li>
    <li>Sensitive columns can be dumped with fake data.</li>
    <li>Conditions can be added to data querying statements.</li>
    <li>With PDO support by default but extensive to many kinds of database toolkits.</li>
    <li>More in the future ...</li>
</ol>

<h2>Requirements</h2>

<ol>
    <li>A web server configured with PHP5 support.</li>
    <li>PDO feature of PHP.</li>
    <li>PHP 5.x, notice that some versions in 5.3.x series have a <a href="http://bugs.php.net/bug.php?id=47332">bug in function parse_ini_file(), which may cause trouble.</a></li>
</ol>

<h2>Limitations (Known Issues)</h2>

<ol>
    <li>Conditions for data querying only support numeric columns and equation relation.</li>
    <li>Support for auto increment columns has not been implemented.</li>
    <li>Sensitive columns must be prefixed with table names.</li>
    <li>Needless columns must be specified only with column names, nothing more.</li>
    <li>Data types which have not been tested may fail dumping.</li>
</ol>

<h2>Installation Instructions</h2>

<ol>
    <li>Download a stable release from GitHub or clone a git repo:</li>
    <ul>
        <li><code>git clone git://github.com/xbot/Dumperor.git</code></li>
    </ul>
    <li>Copy files to a folder which can be visited by the web server, e.g. www/dumperor.</li>
    <li>Rename dumperor.ini.sample to dumperor.ini and change the settings to meet your needs.</li>
    <li>Visit Dumperor from the web browser to start dumping.</li>
</ol>

<h2>News</h2>

<ol>
    <li>2011-03-14 v1.0.0 released. <a href="https://github.com/xbot/Dumperor/wiki/Changelog">changelog</a></li>
    <li>2010-09-22 v0.1a released. <a href="https://github.com/xbot/Dumperor/wiki/Changelog">changelog</a></li>
</ol>
