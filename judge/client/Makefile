all: client.cc
	g++ -Wall -c -I/usr/local/webserver/mysql/include -I/usr/include/mysql client.cc
	g++ -Wall -o client client.o -L/usr/local/webserver/mysql/lib -L/usr/lib/mysql -lmysqlclient
