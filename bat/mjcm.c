#include <stdlib.h>
#include <string.h>
#include <time.h>

int main(int argc, char *argv[])
{
    time_t timer;
    struct tm *local;
    char commit[256];

	// 9���܂ł͍���̓��t
    timer = time(NULL) - 9 * 60 * 60;
    local = localtime(&timer);

    strftime(commit, 256, "git commit -m \"%F", local);
    if (argc >= 2) strcat(commit, argv[1]);
    strcat(commit, "\"");

    system("git add -A");
    system(commit);

    return 0;
}
