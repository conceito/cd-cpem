<?php namespace app;

use PHPExcel_IOFactory;

class Authors
{
	protected $authors = [];

	private $letterIterator = 'A';

	private $articles = [];


	function __construct($sheet = '')
	{
		$this->parseSheet($sheet);
	}

	public function get()
	{
		return $this->authors;
	}

	/**
	 * map over all cells of a row to extract just what is needed
	 * @param $cellIterator
	 */
	public function addRow($cellIterator)
	{
		$newRow    = [];
		$newAuthor = [];
		$isHeader  = false;

		foreach ($cellIterator as $cell)
		{

			$coord = $cell->getCoordinate();
			$value = $cell->getCalculatedValue();

			if ($this->getLetter($coord) === 'A' && strlen($value) == 1 && $this->letterIterator != $value)
			{
				$this->letterIterator = $value;
				$isHeader             = true;
			}

			$newRow['letter'] = $this->letterIterator;

			if ($this->getLetter($coord) === 'A' && !$isHeader)
			{
				$author              = $this->extractAuthorName($value);
				$newRow['first']     = $author['first'];
				$newRow['middle']    = $author['middle'];
				$newRow['last']      = $author['last'];
				$newRow['full_name'] = $value;
			}
			else if ($this->getLetter($coord) === 'B' && !$isHeader)
			{
				$newRow['pdf_ids'] = $this->extractIds($value);
			}

		}

		$this->authors[] = $newRow;
	}

	private function getLetter($coord)
	{
		return preg_replace('/[^a-zA-Z]/', '', $coord);
	}


	private function parseSheet($sheet)
	{
		$objReader   = PHPExcel_IOFactory::createReader('Excel2007');
		$objPHPExcel = $objReader->load($sheet);
		foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
		{

			if ($worksheet->getTitle() == 'authors')
			{
				//				echo 'Worksheet - ', $worksheet->getTitle(), PHP_EOL;

				foreach ($worksheet->getRowIterator() as $row)
				{
					//					echo '    Row number - ', $row->getRowIndex(), PHP_EOL;

					if ($row->getRowIndex() > 1)
					{
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set

						$this->addRow($cellIterator);

						//						foreach ($cellIterator as $cell)
						//						{
						//							if (!is_null($cell))
						//							{
						//								echo '        Cell - ', $cell->getCoordinate(), ' - ', $cell->getCalculatedValue(), PHP_EOL;
						//							}
						//						}
					}

				}
			}

		}
	}

	private function extractAuthorName($nameString)
	{
		//		Abbott, Patrick Jeffrey
		//		Abd El-Raouf, Mohammed Helmy
		$pNames = preg_split('/, ?/', $nameString);

		$author['first']  = $pNames[0];
		$author['middle'] = '-';
		$author['last']   = $pNames[1];

		return $author;
	}

	private function extractIds($idsString)
	{
		$pIds = preg_split('/, ?/', $idsString);

		$pages = [];

		foreach ($pIds as $num)
		{
			if (is_numeric($num))
			{
				$pages[] = $this->getPdfName($num);
			}

		}

		return $pages;
	}

	public function getPdfName($num)
	{
		$n   = $num / 2;
		$n   = '00' . $n;
		$pdf = substr($n, -3);

		//		$pdf = "{$n}.pdf";

		return $pdf;

	}

	public function setArticles($articles)
	{
		$this->articles = $articles;
	}

	public function articleByPdfId($pdfId)
	{
		foreach ($this->articles as $index => $a)
		{
			if($index == $pdfId)
			{
				return $a['title'];
				break;
			}
		}
	}
}