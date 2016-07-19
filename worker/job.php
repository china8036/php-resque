<?php
class job
{
	public function perform()
	{
        file_put_contents(__DIR__ . '/kk.txt', microtime(), FILE_APPEND);
        fwrite(STDOUT, 'Start job! -> ');
		sleep(1);
		fwrite(STDOUT, 'Job ended!' . PHP_EOL);
	}
}