#!/bin/sh

# Directory to save PIDs.
/bin/mkdir -vp /run/php;
/bin/mkdir -vp /run/sshd;

# Run SSHD.
rm -vf /etc/ssh/sshd_config && \
cp -vf /opt/works/etc/ssh/sshd_config /etc/ssh/sshd_config;
exec /usr/sbin/sshd -D &

# Run PHP.
cp -vf /opt/works/etc/php/7.4/fpm/php-fpm.conf /etc/php/7.4/fpm/php-fpm.conf;
cp -vf /opt/works/etc/php/7.4/fpm/pool.d/www.conf /etc/php/7.4/fpm/pool.d/www.conf;
/usr/sbin/php-fpm7.4 --daemonize --fpm-config /etc/php/7.4/fpm/php-fpm.conf &

apt-fast install -y texlive texlive-base;

wait
