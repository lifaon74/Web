<?php
echo $avr->getIncludes() . EOL();

echo $avr->digital->mode('A0', 0) . EOL(2);

echo $avr->analog->reference('INTERNAL_') . EOL(1);
echo $avr->analog->reference('INTERNAL') . EOL(2);
echo $avr->analog->prescaler("ok") . EOL(1);
echo $avr->analog->prescaler("2") . EOL(2);
echo $avr->analog->pin("i") . EOL(1);
echo $avr->analog->pin("A2") . EOL(2);

echo $avr->analog->read("A2") . EOL(2);

?>

Serial.println(<?php echo $avr->digital->read('A0', 0); ?>, DEC);

<?php

echo $avr->digital->write('A0', 0) . EOL(2);
echo $avr->digital->write(3, 1) . EOL(2);
echo $avr->digital->write('i', 1) . EOL(2);
echo $avr->digital->write('i', 'j') . EOL(2);
?>
