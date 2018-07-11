# sudo service mysql stop
# sudo apt-get purge mysql-server mysql-client mysql-common mysql-server-core-5.5 mysql-client-core-5.5
# sudo rm -rf /etc/mysql /var/lib/mysql
# sudo apt-get autoremove
# sudo apt-get autoclean
echo mysql-apt-config mysql-apt-config/select-server select mysql-5.7 |
sudo debconf-set-selections
wget http://dev.mysql.com/get/mysql-apt-config_0.7.3-1_all.deb
sudo dpkg --install mysql-apt-config_0.7.3-1_all.deb
sudo apt-get update -q
sudo apt-get install -q -y  --allow-unauthenticated -o Dpkg::Options::=--force-confnew mysql-server
mysql_upgrade --force
mysql --version