CC=$(CXX)
CXXFLAGS=-I ../judge_client
OBJS:=$(patsubst %.cc, %.o, $(wildcard *.cc))
LINKOPTS += -L/usr/lib/mysql
LINKOPTS += -lmysqlclient


judge_daemon: $(OBJS)
	$(CXX) $(CXXFLAGS) -o $@ $^ $(LINKOPTS)

clean:
	rm -f *.o
	rm -f judge_daemon