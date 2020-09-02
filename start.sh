#!/bin/sh

# Directory to save PIDs.
/bin/mkdir -vp /run/php;
/bin/mkdir -vp /run/sshd;

# Run SSHD.
/bin/rm -vf /etc/ssh/sshd_config && \
/bin/cp -vf /opt/works/etc/ssh/sshd_config /etc/ssh/sshd_config;
exec /usr/sbin/sshd -D &

# Run PHP.
/bin/mkdir -vp /var/log/php/
/bin/cp -vf /opt/works/etc/php/7.4/fpm/php.ini /etc/php/7.4/fpm/php.ini;
/bin/cp -vf /opt/works/etc/php/7.4/fpm/php-fpm.conf /etc/php/7.4/fpm/php-fpm.conf;
/bin/cp -vf /opt/works/etc/php/7.4/fpm/pool.d/www.conf /etc/php/7.4/fpm/pool.d/www.conf;
/usr/sbin/php-fpm7.4 --daemonize --fpm-config /etc/php/7.4/fpm/php-fpm.conf &

/usr/local/bin/isolate --version || (
/usr/bin/git clone https://github.com/ioi/isolate /root/isolate &&
cd /root/isolate &&
/usr/bin/make;
/usr/bin/sed -i 's/CFLAGS=-std=gnu99/CFLAGS=-O3 -std=gnu99/g' /root/isolate/Makefile;
/usr/bin/sed -i '/num_boxes = 1000/c\num_boxes = 69000' /root/isolate/default.cf;
/usr/bin/make install;
)

/usr/bin/sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/g' /etc/ImageMagick-6/policy.xml;

/bin/chown -vR nobody:nogroup -- /var/www/latex.teainside.org;
/bin/chmod -vR u=r,g=,o= -- /var/www/latex.teainside.org;
/bin/chmod -v  u=rx,g=,o= -- $(/bin/find /var/www/latex.teainside.org -type d);
/bin/chmod -v  u=rwx,g=,o= -- $(/bin/find /var/www/latex.teainside.org/storage/latex -type d);

/usr/bin/sudo -u nobody /usr/local/bin/isolate --box-id 6969 --cleanup;
/usr/bin/sudo -u nobody /usr/local/bin/isolate --box-id 6969 --init;
/bin/chown -vR nobody:invalid -- /var/local/lib/isolate/6969;
/bin/chmod -vR u=rwx,g=rwx,o= -- /var/local/lib/isolate/6969;


/usr/local/sbin/apt-fast install -y texlive-full;

wait
