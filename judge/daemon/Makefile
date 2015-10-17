all: judge.cc
	g++ -Wall -c -I/usr/local/webserver/mysql/include -I/usr/include/mysql judge.cc
	g++ -Wall -o judge judge.o -L/usr/local/webserver/mysql/lib -L/usr/lib/mysql -lmysqlclient
