@echo off
set /p outpuFile="Chemin du fichier en sortie : "
:: -c = compile, -g = debugg, -Os = optimize all
avr-gcc compiled/main.c -o compiled/main.o -c -g -Os -mmcu=atmega328p
avr-gcc compiled/main.o -o compiled/main.elf -g -mmcu=atmega328p 
avr-objcopy -j .text -j .data -O ihex compiled/main.elf %outpuFile%.hex
echo Fichier %outpuFile%.hex correctement cree.
pause