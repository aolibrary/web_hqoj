#include <unistd.h>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <sys/ptrace.h>
#include <sys/wait.h>
#include <signal.h>
#include <fcntl.h>
#include "enabled_syscall.h"
#include "tracer.h"
#include "judger.h"
#include "global.h"
#include "logger.h"
#include "util.h"

Tracer::Tracer(pid_t pidApp)
: pid(pidApp), first_execve(true), before_syscall(true) {}

int Tracer::getProcStatus(const char *mark) {

    FILE *pf;
    char fn[1024], buf[1024];
    int ret = 0;
    sprintf(fn, "/proc/%d/status", pid);
    pf = fopen(fn, "r");
    int m = strlen(mark);
    while (pf && fgets(buf, BUF_SIZE - 1, pf)) {
        buf[strlen(buf) - 1] = 0;
        if (strncmp(buf, mark, m) == 0) {
            sscanf(buf + m + 1, "%d", &ret);
        }
    }
    if (pf) {
        fclose(pf);
    }
    return ret;
}

bool Tracer::allowedFileAccess(const char *path) {

    char realPath[1024];
    realpath(path, realPath);

    if (strncmp(realPath, JUDGE_HOME.c_str(), JUDGE_HOME.size()) == 0) {
        ClientLogger::write("Accessing %s is not allowed", realPath);
        return false;
    }
    return true;
}

int Tracer::readStringFromTracedProcess(unsigned long address, char *buffer, int max_length) {

    for (int i = 0; i < max_length; i += sizeof(long)) {
        long data = ptrace(PTRACE_PEEKDATA, pid, address + i, 0);
        if (data == -1) {
            ClientLogger::write("Fail to read address %d", address + i);
            return -1;
        }
        char* p = (char*) &data;
        for (int j = 0; j < (int) sizeof(long); j++, p++) {
            if (*p && i + j < max_length) {
                if (isprint(*p)) {
                    buffer[i + j] = *p;
                } else {
                    ClientLogger::write("Unrecoginized character 0x %x", (int)(*p));
                    return -1;
                }
            } else {
                buffer[i + j] = 0;
                return 0;
            }
        }
    }
    buffer[max_length] = 0;
    return 0;
}

bool Tracer::handleSyscall(Case *oneCase) {

    char path[1024];

    switch(regs.REG_SYSCALL) {
        case SYS_exit:
        case SYS_exit_group:
            break;
        case SYS_execve:
            if (first_execve) {
                first_execve = 0;
                ptrace(PTRACE_SYSCALL, pid, 0, 0);
                return true;
            }
            break;
        case SYS_brk:
            if (before_syscall) {
                requested_brk = regs.REG_ARG0;
            } else {
                if (regs.REG_RET < requested_brk) {
                    oneCase->result = MEMORY_EXCEEDED;
                    ptrace(PTRACE_KILL, pid, 0, 0);
                    return true;
                }
            }
    #if __WORDSIZE == 64
        case SYS_select:
    #else
        case SYS__newselect:
    #endif
            if (before_syscall) {
                long address = regs.REG_ARG4;
                if (address == 0) {
                    break;
                }
                size_t i;
                memset(path, 0, sizeof(struct timeval));
                for (i = 0; i < sizeof(struct timeval); i += sizeof(long)) {
                    long data = ptrace(PTRACE_PEEKDATA, pid, address + i, 0);
                    long* buf = (long*)&path[i];
                    *buf = data;
                }
                // we only allow "selects" that immediately returns
                struct timeval* t = (struct timeval*)&path;
                if (t->tv_sec != 0 || t->tv_usec != 0) {
                    break;
                }
            }
            ptrace(PTRACE_SYSCALL, pid, 0, 0);
            return true;
        case SYS_kill:
            if (before_syscall) {
                // allow self-kill 
                if ((int) regs.REG_ARG0 != pid || (regs.REG_ARG1 != SIGKILL && regs.REG_ARG1 != SIGFPE))
                    break;
            }
            ptrace(PTRACE_SYSCALL, pid, 0, 0);
            return true;
        case SYS_open:
            if (before_syscall) {
                if (readStringFromTracedProcess(regs.REG_ARG0, path, sizeof(path)) < 0) {
                    break;
                }
                ClientLogger::write("SYS_open %s flag %x", path, regs.REG_ARG1);
                if (!allowedFileAccess(path)) {
                    break;
                }
                regs.REG_ARG1 &= ~( O_WRONLY | O_RDWR | O_CREAT | O_APPEND);
                ptrace(PTRACE_SETREGS, pid, 0, &regs);
            }
            ptrace(PTRACE_SYSCALL, pid, 0, 0);
            return true;
    }
    return false;
}

void Tracer::watch(Case *oneCase, Solution *solution) {

    int tmpMemory, status, sig, exitCode;
    struct rusage ruse;

    while (1) {
        wait4(pid, &status, 0, &ruse);

        // check time
        oneCase->usedTime = (ruse.ru_utime.tv_sec * 1000 + ruse.ru_utime.tv_usec / 1000);
        oneCase->usedTime += (ruse.ru_stime.tv_sec * 1000 + ruse.ru_stime.tv_usec / 1000);
        if (oneCase->usedTime > solution->timeLimit) {
            oneCase->result = TIME_EXCEEDED;
            ptrace(PTRACE_KILL, pid, NULL, NULL);
            break;
        }

        tmpMemory = getProcStatus("VmPeak:")<<10;
        if (tmpMemory > oneCase->usedMemory) {
            oneCase->usedMemory = tmpMemory;
        }

        // MLE
        if (oneCase->usedMemory > solution->memoryLimit*STD_KB) {
            oneCase->result = MEMORY_EXCEEDED;
            ptrace(PTRACE_KILL, pid, NULL, NULL);
            break;
        }
        
        if (WIFEXITED(status)) {
            break;
        }

        // RE
        if (getFilesize("re.txt")) {
            oneCase->result = RUNTIME_ERROR;
            ptrace(PTRACE_KILL, pid, NULL, NULL);
            break;
        }

        // OL
        if (getFilesize(oneCase->userFile.c_str()) > getFilesize(oneCase->outFile.c_str())*2+1024) {
            oneCase->result = OUTPUT_EXCEEDED;
            ptrace(PTRACE_KILL, pid, NULL, NULL);
            break;
        }

        exitCode = WEXITSTATUS(status);
        if (exitCode == 0x05 || exitCode == 0) {
            ;
        } else {
            if (oneCase->result == ACCEPTED) {
                switch (exitCode) {
                    case SIGCHLD:
                    case SIGALRM:
                        alarm(0);
                    case SIGKILL:
                    case SIGXCPU:
                        oneCase->result = TIME_EXCEEDED;
                        break;
                    case SIGXFSZ:
                        oneCase->result = OUTPUT_EXCEEDED;
                        break;
                    default:
                        oneCase->result = RUNTIME_ERROR;
                }
                Judger::logRuntimeError(strsignal(exitCode));
            }
            ptrace(PTRACE_KILL, pid, NULL, NULL);
            break;
        }

        // WIFSIGNALED: if the process is terminated by signal
        if (WIFSIGNALED(status)) {
            sig = WTERMSIG(status);
            if (oneCase->result == ACCEPTED) {
                switch (sig) {
                    case SIGCHLD:
                    case SIGALRM:
                        alarm(0);
                    case SIGKILL:
                    case SIGXCPU:
                        oneCase->result = TIME_EXCEEDED;
                        break;
                    case SIGXFSZ:
                        oneCase->result = OUTPUT_EXCEEDED;
                        break;
                    default:
                        oneCase->result = RUNTIME_ERROR;
                }
                Judger::logRuntimeError(strsignal(sig));
            }
            break;
        }

        before_syscall = !before_syscall;

        // check the system calls
        ptrace(PTRACE_GETREGS, pid, NULL, &regs);

        if (handleSyscall(oneCase)) {
            continue;
        }

        if (regs.REG_SYSCALL < sizeof(enabled_syscall) / sizeof(enabled_syscall[0])
        && !enabled_syscall[regs.REG_SYSCALL]) {
            oneCase->result = RUNTIME_ERROR;
            char error[BUF_SIZE];
            sprintf(error, "Restricted syscall %s", syscall_name[regs.REG_SYSCALL]);
            Judger::logRuntimeError(error);
            ptrace(PTRACE_KILL, pid, NULL, NULL);
            break;
        }
        ptrace(PTRACE_SYSCALL, pid, NULL, NULL);
    }
    
    // fixed
    if (oneCase->result == TIME_EXCEEDED && oneCase->usedMemory == 0) {
        oneCase->result = MEMORY_EXCEEDED;
    }
    if (oneCase->result == TIME_EXCEEDED && oneCase->usedTime <= solution->timeLimit) {
        oneCase->usedTime = solution->timeLimit+1;
    }
}