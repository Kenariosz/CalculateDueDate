<?php

/**
 * @author: Kenariosz
 * @date  : 2016-10-22
 */
class CalculateDueDate {

	private $aDayInHour = 8;
	private $returnFormat = 'Y-m-d H:i:s';
	private $dateTime = '';
	private $resolveTime = '';

	private $error = [
		'status'  => false,
		'message' => '',
	];


	public function __construct(DateTime $dateTime, $returnFormat)
	{
		$this->dateTime = $dateTime;
		$this->returnFormat = $returnFormat;

		$this->setResolveTime();
	}

	public function __toString()
	{
		return $this->resolveTime->format($this->returnFormat);
	}

	/**
	 * Set resolve time
	 */
	private function setResolveTime()
	{
		$this->resolveTime = $this->calculateResolveTime();
	}

	/**
	 * @return \DateTime
	 * @throws \Exception
	 */
	private function calculateResolveTime()
	{
		if($this->isWorkTime() and $this->isWorkDay())
		{
			return $this->incrementDay();
		}
		else
		{
			throw new Exception('Sajnáljuk, de munkaidőn kívűl nem fogadunk bejelentést.');
		}
	}

	/**
	 * Increment the date depend on day.
	 *
	 * @return \DateTime
	 */
	private function incrementDay()
	{
		if($this->isFriday())
		{
			return $this->dateTime->add(new DateInterval('P4D'));
		}
		else
		{
			return $this->dateTime->add(new DateInterval('P2D'));
		}
	}

	/**
	 * Check friday.
	 *
	 * @return bool
	 */
	private function isFriday()
	{
		if($this->dateTime->format("w") == 5)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check working day
	 *
	 * @return bool
	 */
	private function isWorkDay()
	{
		if(0 < $this->dateTime->format("w") AND $this->dateTime->format("w") < 6)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check working time. (09:00:00 - 17:00:00)
	 *
	 * @return bool
	 */
	private function isWorkTime()
	{
		$endDate = new DateTime('17:00:00');

		$diff = $endDate->getTimestamp() - $this->dateTime->getTimestamp();
		// check day
		if($diff >= 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

/**
 *
 * EXAMPLE
 *
 */
try
{
	$format = 'Y-m-d H:i:s';
	$date = DateTime::createFromFormat($format, '2016-10-22 17:00:00');
	$dueDate = new CalculateDueDate($date, $format);

	echo $dueDate;
}
catch(Exception $e)
{
	echo 'Caught exception: ', $e->getMessage(), "\n";
}