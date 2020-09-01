#!/bin/sh

apt-get update -y \
&& \
apt-get install -y locales apt-utils tzdata \
&& \
locale-gen en_US.UTF-8 \
&& \
/bin/echo -e 'debconf debconf/frontend select Noninteractive' | debconf-set-selections \
&& \
/bin/echo -e "tzdata tzdata/Areas select Asia\ntzdata tzdata/Zones/Asia select Jakarta" > /tmp/tz \
&& \
rm -rfv /etc/localtime /etc/timezone \
&& \
debconf-set-selections /tmp/tz \
&& \
localedef -i en_US -c -f UTF-8 -A /usr/share/locale/locale.alias en_US.UTF-8 \
&& \
dpkg-reconfigure -f noninteractive tzdata \
&& \
apt-get install curl sudo wget -y \
&& \
/bin/bash -c "$(curl -sL https://git.io/vokNn)" \
&& \
apt-fast install -y \
php7.4 php7.4-fpm php7.4-cli php7.4-common php7.4-opcache ssh texlive-full \
&& \
mkdir -v /root/.ssh \
&& \
/bin/echo -e "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQDJ6wRDzU3vZClJOtXHZvocGHHTgHzSkXOyN7i+S+Nu7msxsUMl+IKj/Nkcaemq6+kDw7Yu2jf43qQFAU+xH6RHlOraqcVGmCgAWDflvMQGVm0mVnDrEVQIJl7G+dNpf9nbXFG5n+HArtKkmwes8sryiYPGHK2TzBLwQ6MyWUPlxe1P3VO7iwyBFovwDiz8b50dGi4n+2HAaf9AM3MbXveIsV9hXBLf1sFxG9fL26Ye67n3N16wmCx3k5b61FNteQ3R5AenwdAsExwaEtFttjQIUsTUx8OGLXrVyTfsG7WMwFnk0ZUu9sV7f++FTACNboXPhFJLs6esgnqmKTwqFBafzZ9NQWH/weD7xbTU/jBmbmITs/hlCWRtcoj+Gn1Cm5l+eQsGuN9X3inG2cDkaSv9eaTCkW2ijRjbiKaoqbgHE5FfXAmQz0I4AWioTNQyOIo2SYsbSSQM/RjCVQJ4gS17vjdd+m/nuNh/h0j7dgR7MjqFY5q0M+1I4P7/zpyARYE= root@esteh\nssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQDhPO9suo2UqHBayA/QpVE7lJ17jShGg6fHQSOFFkpA1n+14vcVoOu/biYQh6EampR0pR/VMilxUhoPm3LQnjI1w7ZvzsndTG/M6qjOeEjn3PbchcrPWhFk+JhrJVczg9iWCbDxz9NMUncHBsYd8e34hxvuIBiWTAUFs7cIiS0gzyE1bPwIzSvlyX8wxYeHrn1GjOioaLACmzJ6ngw9ex/RhjpLDiUkDQE5qhi+tIHbCLn/IOEkDeNnirkCkXn3brjjps3gvCsKv1GmgLGG2MU3wGqdEu78eqwvZ2t3sF2TKol7Ymthe/romWQ7XpeeUdn/6KxfvFCkGEpmeP3Fb9RQ5e53XSpDq42Te7UhKZqxzmZKl5IcAEKqm5Ozsu8FLDWAjYuUF71W8az19iBCNrNdMPjyR1wG04peIQyYmaYjZuUFTDMLMozIxuUynaTR0SuL6u49s0Z6mX2n69Z7vjwCrx5WkE64BJqBbRXs4R5lme+pf50PM8ZyLZEbdtMh/6fNpRhjEOA+9hwotk0MN+v2sKukxZj1JpMWd4yl1XRrIPUH73OxLQcwsVzNJzdzanORFEvPqT6qXHzvkE67QSChiRZyYQGWidfpANEJlNKaLjDRskOzBQ1SOBB4qwpRYkp2J5h5SH4NgGd9vDdpoUXYcN+tzrsqFwInNqIPlzeWnQ== ammarfaizi2@gmail.com\nssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQDP0LtpQrBzIE3flPQAofSTjuG5NTbSwHlo6YdBcoRoTeIC0IC4nzYvi2nl0HMC2EItejU+ghAeEaC+DtGFCJLSwcp0c7uBs+OexUAKb+T/vKNVkNH7Uj32c5fedOlBy+AM7RmH/BrQDe9L9yDu66eh8ox62G7DLYdE9Rd843w+MoINEbBn33OQnQk8wegaDyrZ/exGWn8d769lINKMx8nF3qr5j/N/cFH8w7wkMv3zvrj0ma9VbJvnXXeKZUr+pCQnCSnSoB8yQFroJ4Ql0rCIKMfCiwCzkku3iaOa7q+OVUfyA0M6OZF2W4oUJBkxe4lej638GjmHjaVgfU2ElM+gDQng7pKyO/i5OMJ3A56YGPzLrBB+uEOqEfz3AjbvwVyL6i5OfFtprRcDaRu5dnI1z0F/hkSkXcs59nWxE8cXzXpGwWWN6I6soItarwsY//7HF2nK1GWSdiYytWizdyvR5T9YiTKeMWjKzgEq40g9mymKTahPVN2nb9en2hVj7Uk= root@esteh" > /root/.ssh/authorized_keys \
&& \
chmod -vR 600 /root/.ssh;
