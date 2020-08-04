#include <stdio.h>
#include <unistd.h>
#include<stdlib.h>
int main()
{
        pid_t pid;
        printf("Now only one process\n");
        printf("Calling fork…\n");
        pid=fork();
        if (!pid)
        printf("I’m the child\n");
        else if (pid>0)
        printf("I’m the parent, child has pid %d\n",pid);
        else
        printf("Fork fail!\n");
}
