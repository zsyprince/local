#include "csapp.h"

//此程序是使用基于 IO多路复用的并发服务器

void echo(int connfd)
{
    int n;
    char buf[MAXLINE];
    rio_t rio;
    
    rio_readinitb(&rio,connfd);
    //带缓冲的读取函数
    while((n=rio_readlineb(&rio,buf,MAXLINE))>0) {
        //向连接符写入内容
        printf("server received %d bytes \n",n);
        rio_writen(connfd,buf,n);
    }
}


/*command是作为键盘输入时执行的驱动动作*/
void command(void) {
    char buf[MAXLINE];
    printf("you input just now!\n");
    //从标准输入中读取输入到buf中存储
    if(!fgets(buf,MAXLINE,stdin))
        exit(0);
    //输出buf中的数据
    printf("%s",buf);
}

//主程序
int main(int argc,char **argv)
{   
    //监听符，连接符，端口号
    int listenfd,connfd,port;
    //套接字地址结构的大小
    socklen_t clientlen=sizeof(struct sockaddr_in);
    //新建套接字地址结构
    struct sockaddr_in clientaddr;
    //fd_set为描述符集合，此处定义了两个read_set,ready_set描述符集合,分别是读集合/准备好集合
    fd_set read_set,ready_set;
    
    //如果运行时参数小于2，则提示错误
    if(argc!=2) {
        fprintf(stderr,"usage :%s <port>\n",argv[0]);
        exit(0);
    }

    //将第二个参数转化为整型端口号，args to integer
    port=atoi(argv[1]);

    //打开端口号，返回监听描述符
    listenfd=open_listenfd(port);
    
    //清空读集合
    FD_ZERO(&read_set);
    
    //将标准输入加到读集合
    FD_SET(STDIN_FILENO,&read_set);

    //将监听描述符加到读集合
    FD_SET(listenfd,&read_set);

    //服务器监听处理主程序
    while(1) {

        //将读集合赋值给准备好集合
        ready_set=read_set;

        //select函数会要求内核挂起进程，等待一个或多个IO事件发生后，才将控制返回给应用程序，就像在下面的示例一样    
        select(listenfd+1,&ready_set,NULL,NULL,NULL);

        //有IO事件后，将判断是来自从键盘上键入命令还是从客户端发来的请求，分别给出不同的回应
        if(FD_ISSET(STDIN_FILENO,&ready_set))
            command();
        if(FD_ISSET(listenfd,&ready_set)) {
            connfd=accept(listenfd,(SA *)&clientaddr,&clientlen);
            printf("client connected!");
            //向连接符回送数据
            echo(connfd);
            //关闭连接符，释放资源
            close(connfd);
        }
    }
}
