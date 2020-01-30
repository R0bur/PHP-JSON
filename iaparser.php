<?php
/*============================================================*/
/* Набор основных функций для синтаксического разбора текста. */
/* Автор: Игорь Сергеевич Орещенков, февраль 2017 г.          */
/* Среда разработки: PHP 5.6.23 + AkelPad 4.9.7               */
/*============================================================*/
/*=====================================================================*/
/* Пропуск символов в строке.                                          */
/* Вызов: &$s - ссылка на обрабатываемую строку,                       */
/*        $t  - строка, содержащая пропускаемые символы,               */
/*        $p  - номер позиции в строке, с которой начинается обработка */
/*              (допускается FALSE в качестве конца строки),           */
/*        $l  - длина обрабатываемой строки.                           */
/* Возврат: номер позиции после пропущенных символов или               */
/*          FALSE, если достигнут конец строки.                        */
/*---------------------------------------------------------------------*/
/* Skip characters in a string.                                        */
/* Call: &$s - the reference to the string for review,                 */
/*        $t - the string containing set of characters to skip for,    */
/*        $p - starting position in the $s (FALSE treated as end       */
/*             of string),                                             */
/*        $l - the length of the string $s (for speedup only).         */
/* Return: the number of position of the first found character or      */
/*         FALSE if end of the string $s occurred.                     */
/*=====================================================================*/
function iaSkipOver (&$s, $t, $p = 0, $l = FALSE)
{
	if ($l == FALSE):
		$l = strlen ($s);
	endif;
	if ($p !== FALSE):
		while ($p < $l and strpos ($t, $s[$p]) !== FALSE):
			$p++;
		endwhile;
		if ($p >= $l):
			$p = FALSE;
		endif;
	endif;
	return $p;
}
/*=====================================================================*/
/* Поиск одного из символов в строке.                                  */
/* Вызов: &$s - ссылка на обрабатываемую строку,                       */
/*        $t  - строка, содержащая искомые символы,                    */
/*        $p  - номер позиции в строке, с которой начинается обработка */
/*              (допускается FALSE в качестве конца строки),           */
/*        $l  - длина обрабатываемой строки,                           */
/* Возврат: номер позиции первого найденного символа или               */
/*          FALSE, если достигнут конец строки.                        */
/*---------------------------------------------------------------------*/
/* Find one of any specified character in a string.                    */
/* Call: &$s - the reference to the string to search in,               */
/*        $t - the string containing set of characters to search for,  */
/*        $p - starting position in the $s (FALSE is treated           */
/*             as end of string),                                      */
/*        $l - the length of the string $s (for speedup only).         */
/* Return: the number of position of the first found character or      */
/*         FALSE if end of the string $s occurred.                     */
/*=====================================================================*/
function iaSkipTo (&$s, $t, $p = 0, $l = FALSE) {
	$l = strlen ($s);
	if ($p !== FALSE):
		while ($p < $l and strpos ($t, $s[$p]) === FALSE):
			$p++;
		endwhile;
		if ($p >= $l):
			$p = FALSE;
		endif;
	endif;
	return $p;
}
?>