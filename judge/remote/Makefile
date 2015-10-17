all: remote-pm.cc
	g++ -Wall -c -I/usr/local/webserver/mysql/include -I/usr/include/mysql remote-pm.cc
	g++ -Wall -o remote-pm remote-pm.o -L/usr/local/webserver/mysql/lib -L/usr/lib/mysql -lmysqlclient
