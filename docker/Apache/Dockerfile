FROM debian:buster-slim

RUN apt update && apt install -y apache2
RUN a2enmod proxy_fcgi
RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/

COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf

CMD /usr/sbin/apache2ctl -D FOREGROUND

EXPOSE 80