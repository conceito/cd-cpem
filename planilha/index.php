<?php
require_once "vendor/autoload.php";
?>

	<a href="?app=authors">Por autores</a>


<?php
if (isset($_GET['app']) && $_GET['app'] == 'authors'):

	$authors = new \app\Authors("authors.xlsx");
	$mySheet = new \app\Sheet("articles.xlsx");

	$authors->setArticles($mySheet->get());

	foreach ($authors->get() as $a):

		if (! isset($a['full_name'])):
			?>
			<h2><?php echo $a['letter']?></h2>
		<?php
		else:
			?>

			<div class="list-item">
				<p class="author author-name"><?php echo $a['full_name'] ?></p>

				<?php
				foreach ($a['pdf_ids'] as $pdfId):
					?>
					<a href="../papers/<?php echo $pdfId ?>.pdf#page=1"><?php echo $authors->articleByPdfId($pdfId) ?></a>
				<?php
				endforeach;
				?>

			</div>



		<?php

		endif;

	endforeach;

endif;

if (isset($_GET['app']) && $_GET['app'] == 'articles'):

	$mySheet = new \app\Sheet("articles.xlsx");

	?>

	<pre>
		<?php print_r($mySheet->get()); ?>
	</pre>

<?php
endif;

?>