<?php namespace app;

use PHPExcel_IOFactory;

class Sheet
{
	private $firstNameLetters = ['H', 'K', 'N', 'Q', 'T', 'W', 'Z', 'AC', 'AF', 'AI', 'AL', 'AO', 'AR', 'AU', 'AX', 'BA', 'BD', 'BG', 'BJ', 'BM', 'BP', 'BS', 'BV'];
	private $middleNameLetters = ['I', 'L', 'O', 'R', 'U', 'X', 'AA', 'AD', 'AG', 'AJ', 'AM', 'AP', 'AS', 'AV', 'AY', 'BB', 'BE', 'BH', 'BK', 'BN', 'BQ', 'BT', 'BW'];
	private $lastNameLetters = ['J', 'M', 'P', 'S', 'V', 'Y', 'AB', 'AE', 'AH', 'AK', 'AN', 'AQ', 'AT', 'AW', 'AZ', 'BC', 'BF', 'BI', 'BL', 'BO', 'BR', 'BU', 'BX'];

	protected $rows = [];

	function __construct($sheet = '')
	{
		$this->parseSheet($sheet);
	}

	/**
	 * map over all cells of a row to extract just what is needed
	 * @param $cellIterator
	 */
	public function addRow($cellIterator)
	{
		$newRow = [];
		$newAuthor = [];

		foreach ($cellIterator as $cell)
		{

			$coord = $cell->getCoordinate();
			$value = $cell->getCalculatedValue();

			if ($this->getLetter($coord) === 'A')
			{
				$newRow['order'] = $value;
				$newRow['pdf_id'] = $this->getPdfName($value);
			}
			else if ($this->getLetter($coord) === 'B')
			{
				$newRow['paper_id'] = $value;
			}
			else if ($this->getLetter($coord) === 'C')
			{
				$newRow['title'] = $value;
			}
			/**
			 * ======================================================
			 * authors
			 */

//			else if ($this->isFirstName($coord))
//			{
//				$newAuthor['first'] = (mb_strlen($value) < 2) ? null : $value;
//			}
//			else if ($this->isMiddleName($coord))
//			{
//				$newAuthor['middle'] = (mb_strlen($value) < 2) ? null : $value;
//			}
//			else if ($this->isLastName($coord))
//			{
//				$newAuthor['last'] = (mb_strlen($value) < 2) ? null : $value;
//				$newRow['authors'][] = $newAuthor;
//			}

		}

		$this->rows[$newRow['pdf_id']] = $newRow;
	}

	private function isFirstName($coord)
	{
		$l = $this->getLetter($coord);

		return in_array($l, $this->firstNameLetters);
	}

	private function isMiddleName($coord)
	{
		$l = $this->getLetter($coord);

		return in_array($l, $this->middleNameLetters);
	}

	private function isLastName($coord)
	{
		$l = $this->getLetter($coord);

		return in_array($l, $this->lastNameLetters);
	}

	private function getLetter($coord)
	{
		return preg_replace('/[^a-zA-Z]/', '', $coord);
	}


	/**
	 * return all rows
	 *
	 * @return array
	 */
	public function get()
	{
		return $this->rows;
	}

	public function getPdfName($order)
	{
		$n = '00' . $order;
		$pdf = substr($n, -3);

		return $pdf;
	}


	private function parseSheet($sheet)
	{
		$objReader   = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load($sheet);
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{

			if ($worksheet->getTitle() === 'Todas')
			{
				//			echo 'Worksheet - ', $worksheet->getTitle(), PHP_EOL;

				foreach ($worksheet->getRowIterator() as $row)
				{
					//				echo '    Row number - ', $row->getRowIndex(), PHP_EOL;

					if($row->getRowIndex() > 1)
					{
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set

						$this->addRow($cellIterator);

//						foreach ($cellIterator as $cell)
//						{
//							if (!is_null($cell))
//							{
//								//							echo '        Cell - ', $cell->getCoordinate(), ' - ', $cell->getCalculatedValue(), PHP_EOL;
//							}
//						}
					}


				}
			}

		}
	}
}