<?php
/*====================================================*/
/* Набор функций для разбора документов JSON.         */
/* Автор: Игорь Сергеевич Орещенков, январь 2019 г.   */
/* Среда разработки: PHP 5.6.23 + AkelPad 4.9.7       */
/*====================================================*/
require_once 'iaparser.php';
/*========================================================*/
/* Декодирование JSON в массив PHP.                       */
/* Вызов: $json - строка, содержащая JSON.                */
/* Возврат: массив, содержащий декодированную информацию  */
/*          или FALSE в случае ошибки.                    */
/*--------------------------------------------------------*/
/* Decode JSON string to PHP array.                       */
/* Call: $json - the string containing JSON.              */
/* Return: "keys-values" PHP array.                       */
/*========================================================*/
function iaJsonDecode (&$json) {
	$SPACES = " \t\r\n";
	$STRLEN = strlen ($json);
	$a = FALSE;
	$p = 0;
	/* Запуск модуля разбора в зависимости от текущего контекста. */
	$p = iaSkipOver ($json, $SPACES, $p, $STRLEN);
	if ($p !== FALSE):
		switch ($json{$p}):
			case '[': // массив
				$a = iaJsonArray ($json, $p, $SPACES, $STRLEN);
				break;
			case '{': // объект
				$a = iaJsonObject ($json, $p, $SPACES, $STRLEN);
				break;
		endswitch;
	endif;
	return $a;
}
/*=================================================*/
/* Блок разбора JSON-массива.                      */
/* Вызов: $json - строка, содержащая JSON.         */
/*        $p    - номер текущей позиции в строке,  */
/*        $SPACES - пробельные символы,            */
/*        $STRLEN - длина строки $json.            */
/* Возврат: массив с декодированной информацией.   */
/*-------------------------------------------------*/
/* Subroutine for a JSON-array "[...]" parsing.    */
/* Call: $json - the string containing JSON,       */
/*       $p    - the number of starting position   */
/*               in the string $json,              */
/*       $SPACES - the string of space characters, */
/*       $STRLEN - the length of the string $json. */
/* Return: array of the decoded JSON-array.        */
/*=================================================*/
function iaJsonArray (&$json, &$p, $SPACES, $STRLEN) {
	$a = array ();
	$p = iaSkipOver ($json, $SPACES, $p + 1, $STRLEN);
	$c = ($p !== FALSE? $json{$p}: '');
	while ($p !== FALSE and $c !== ']'):
		/* Декодирование элемента массива. */
		switch ($c):
			case '[': // массив
				$r = iaJsonArray ($json, $p, $SPACES, $STRLEN);
				break;
			case '{': // объект
				$r = iaJsonObject ($json, $p, $SPACES, $STRLEN);
				break;
			default: // элемент массива.
				$r = iaJsonScalar ($c, ',]', $json, $p, $SPACES, $STRLEN);
		endswitch;
		/* Обработка результата декодирования элемента массива. */
		if ($r !== FALSE):
			$a[] = $r;
			$p = iaSkipOver ($json, $SPACES, $p, $STRLEN);
			$c = ($p !== FALSE? $json{$p}: '');
			if ($c == ','):
				$p = iaSkipOver ($json, $SPACES, $p + 1, $STRLEN);
				$c = ($p !== FALSE? $json{$p}: '');
			endif;
		else:
			$p = FALSE;
		endif;
	endwhile;
	/* Обработка результата декодирования массива. */
	if ($c == ']'):
		$p++;
	else:
		$a = FALSE;
	endif;
	return $a;
}
/*=================================================*/
/* Блок разбора JSON-объекта.                      */
/* Вызов: $json - строка, содержащая JSON.         */
/*        $p    - номер текущей позиции в строке,  */
/*        $SPACES - пробельные символы,            */
/*        $STRLEN - длина строки $json.            */
/* Возврат: массив с декодированной информацией.   */
/*-------------------------------------------------*/
/* Subroutine for a JSON-object "{...}" parsing.   */
/* Call: $json - the string containing JSON,       */
/*       $p    - the number of starting position   */
/*               in the string $json,              */
/*       $SPACES - the string of space characters, */
/*       $STRLEN - the length of the string $json. */
/* Return: array of the decoded JSON-object.       */
/*=================================================*/
function iaJsonObject (&$json, &$p, $SPACES, $STRLEN) {
	$a = array ();
	$p = iaSkipOver ($json, $SPACES, $p + 1, $STRLEN);
	$c = ($p !== FALSE? $json{$p}: '');
	while ($p !== FALSE and $c !== '}'):
		$k = iaJsonScalar ($c, ':', $json, $p, $SPACES, $STRLEN);
		$c = $k !== FALSE? $json{$p}: '';
		if ($c == ':'):
			$p = iaSkipOver ($json, $SPACES, $p + 1, $STRLEN);
			if ($p !== FALSE):
				/* Декодирование значения свойства объекта. */
				$c = $json{$p};
				switch ($c):
					case '[': // массив
						$r = iaJsonArray ($json, $p, $SPACES, $STRLEN);
						break;
					case '{': // объект
						$r = iaJsonObject ($json, $p, $SPACES, $STRLEN);
						break;
					default: // скалярное значение
						$r = iaJsonScalar ($c, ',}', $json, $p, $SPACES, $STRLEN);
				endswitch;
				/* Обработка результата декодирования значения свойства объекта. */
				if ($r !== FALSE):
					$a[$k] = $r;
				else:
					$p = FALSE;
				endif;
			endif;
			if ($p !== FALSE):
				$p = iaSkipOver ($json, $SPACES, $p, $STRLEN);
				$c = ($p !== FALSE? $json{$p}: '');
				if ($c == ','):
					$p = iaSkipOver ($json, $SPACES, $p + 1, $STRLEN);
					$c = ($p !== FALSE? $json{$p}: '');
				endif;
			endif;
		else:
			$p = FALSE;
		endif;
	endwhile;
	/* Обработка результата декодирования объекта. */
	if ($c == '}'):
		$p++;
	else:
		$a = FALSE;
	endif;
	return $a;
}
/*=====================================================*/
/* Блок разбора JSON-скаляра                           */
/* Вызов: $c - первый символ скаляра,                  */
/*        $EOW - символы-признаки завершения скаляра,  */
/*        $json - строка, содержащая JSON,             */
/*        $p    - номер текущей позиции в строке,      */
/*        $SPACES - пробельные символы,                */
/*        $STRLEN - длина строки $json.                */
/* Возврат: значение скаляра или FALSE, если в записи  */
/*          значения скаляра допущена ошибка.          */
/* После возврата текущим становится символ, непосред- */
/* ственно следующий после скаляра.                    */
/*-----------------------------------------------------*/
/* Subroutine for a JSON-scalar parsing.               */
/* Call: $c - the first character of the JSON-scalar,  */
/*       $EOW - scalar terminators,                    */
/*       $json - the string containing JSON,           */
/*       $p    - the number of starting position       */
/*               in the string $json,                  */
/*       $SPACES - the string of space characters,     */
/*       $STRLEN - the length of the string $json.     */
/* Return: value of the decoded JSON-scalar.           */
/*=====================================================*/
function iaJsonScalar ($c, $EOW, &$json, &$p, $SPACES, $STRLEN) {
	if ($c == '"' or $c == '\''):
		/* Значение скаляра заключено в кавычки. */
		$p1 = $p + 1;
		do {
			$p = $p + 1;
			$p = iaSkipTo ($json, $c, $p, $STRLEN);
		} while ($p !== FALSE and $json[$p - 1] == '\\');
		$v = $p !== FALSE? stripcslashes (substr ($json, $p1, $p - $p1)): FALSE;
		$p++;
	else:
		/* Значение скаляра записано без кавычек. */
		$p1 = $p;
		$p = iaSkipTo ($json, "$EOW$SPACES", $p1, $STRLEN);
		$v = $p !== FALSE? substr ($json, $p1, $p - $p1): FALSE;
	endif;
	return $v;
}
?>