FROM php:7.4.2-apache
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN apt-get update
RUN apt-get install -y wget
RUN apt-get install -y vim
RUN apt-get install -y systemd

#install beats
RUN apt-get update && apt-get install -y gnupg2
RUN wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | apt-key add -
RUN apt-get install apt-transport-https
RUN echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" | tee -a /etc/apt/sources.list.d/elastic-7.x.list
RUN apt-get update && apt-get -y install metricbeat && apt-get -y install filebeat

#configure beats
COPY metricbeat.yml /etc/metricbeat/metricbeat.yml
RUN chown root /etc/metricbeat/metricbeat.yml

COPY filebeat.yml /etc/filebeat/filebeat.yml
RUN chown root /etc/filebeat/filebeat.yml

WORKDIR /var/log/apache2

RUN rm *
RUN touch access.log error.log other_vhosts_access.log
RUN chown www-data:www-data access.log error.log other_vhosts_access.log
RUN chmod 777 access.log error.log other_vhosts_access.log
RUN apache2ctl restart

WORKDIR /var/www/html


COPY start.sh /var/start.sh
RUN chmod 755 /var/start.sh
CMD ["/var/start.sh"]