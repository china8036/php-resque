<?php


class job implements core\intface\Job
{
	public function perform()
	{
        fwrite(STDOUT, 'Start job! -> ');
		sleep(1);
		fwrite(STDOUT, 'Job ended!' . PHP_EOL);
	}
}