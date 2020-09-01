
FROM ubuntu:20.04
WORKDIR /

MAINTAINER Ammar Faizi <ammarfaizi2@gmail.com>

# Copy required files.
COPY start.sh /opt/start.sh
COPY install.sh /opt/install.sh

# Set timezone and locale
ARG DEBIAN_FRONTEND=noninteractive
ENV LANG en_US.UTF-8  
ENV LANGUAGE en_US:en  
ENV LC_ALL en_US.UTF-8

# Run installation.
RUN	chmod -v +x /opt/install.sh && chmod -v +x /opt/start.sh && /opt/install.sh


# Run the container.
CMD /opt/start.sh
