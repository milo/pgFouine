This is a pgFouine source code downloaded from http://pgfoundry.org/frs/?group_id=1000152 and hot-fixed for PHP 7.1.

Would be better to keep whole CVS history but today:
```
# cvs -d :pserver:anonymous@cvs.pgfoundry.org:/var/lib/gforge/chroot/cvsroot/pgfouine login
cvs [login aborted]: connect to cvs.pgfoundry.org(188.227.186.71):2401 failed: Connection refused
```

Since this repo arose from pgFouine v1.2, its versions will be v1.2-1, v1.2-2, and so on.


Original README
===============
pgFouine is a PostgreSQL log analyzer designed to parse
big log files with a low memory footprint.
You can generate text or HTML reports containing aggregated
information about the queries executed by your database.

Example:
```
pgfouine.php -file path/to/your/log/file.log > report.html
```

For comprehensive usage information, just run:
```
pgfouine.php -help
```
