#ifndef __TRACER_H__
#define __TRACER_H__

#ifdef __i386
#define REG_SYSCALL orig_eax
#define REG_RET eax
#define REG_ARG0 ebx
#define REG_ARG1 ecx
#define REG_ARG4 edi
#else
#ifdef __x86_64
#define REG_SYSCALL orig_rax
#define REG_RET rax
#define REG_ARG0 rdi
#define REG_ARG1 rsi
#define REG_ARG4 r8
#endif
#endif

#include <sys/user.h>
#include "case.h"
#include "solution.h"

class Tracer {
public:
    Tracer(pid_t pidApp);
    void watch(Case *oneCase, Solution *solution);



    pid_t pid;
    bool first_execve, before_syscall;
    unsigned long requested_brk;
    struct user_regs_struct regs;
private:
    int getProcStatus(const char *mark);
    bool allowedFileAccess(const char *path);
    int readStringFromTracedProcess(unsigned long address, char *buffer, int max_length);
    bool handleSyscall(Case *oneCase);
};

#endif