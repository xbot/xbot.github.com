# Archlinux 下在 /var/run 下创建目录的方法


Archlinux 使用 [systemd-tmpfiles](https://wiki.archlinux.org/title/systemd#systemd-tmpfiles_-_temporary_files) 管理 `/var/run` 下的临时目录。这意味着如果我手动创建 `/var/run/php` 目录，在系统重启后将不复存在。

解决的办法是修改 `/usr/lib/tmpfiles.d/php-fpm.conf` 文件，把默认的目录名 `php-fpm` 改成 `php`。

但这会带来另一个问题，当 PHP 被重装或升级后，该文件会被覆盖。所以，解决的方法是复制后再修改。

