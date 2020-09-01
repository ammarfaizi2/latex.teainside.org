#!/bin/sh

# Directory to save PIDs.
/bin/mkdir -vp /run/php;
/bin/mkdir -vp /run/sshd;

# Run SSHD.
rm -vf /etc/ssh/sshd_config && \
cp -vf /opt/works/etc/ssh/sshd_config /etc/ssh/sshd_config;
exec /usr/sbin/sshd -D &

# Run PHP.
rm -rfv /etc/php;
ln -sfv /opt/works/etc/php /etc/php;
/usr/local/sbin/php-fpm -y /etc/php/7.4/fpm/php-fpm.conf --daemonize -R &

wait
