CC=$(CXX)
OBJS:=$(patsubst %.cc, %.o, $(wildcard *.cc))
LINKOPTS += -L/usr/lib/mysql
LINKOPTS += -lmysqlclient

judge_client: $(OBJS)
	$(CXX) $(CXXFLAGS) -o $@ $^ $(LINKOPTS)

clean:
	rm -f *.o
	rm -f judge_client