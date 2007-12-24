rmdir /q/s deploy
mkdir deploy
xcopy webim deploy\ /s/q
rmdir /q/s deploy\.settings
del deploy\.project
