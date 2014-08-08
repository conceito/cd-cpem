<?php header('Content-Type: text/html; charset=utf-8');
require_once "vendor/autoload.php";

if (!isset($_GET['app'])):
	?>
	<a href="?app=authors">Por autores</a> | <a href="?app=articles">Por artigo</a>
<?php
endif;
?>

<?php
/**
 * ==================================================
 * Authors
 */
if (isset($_GET['app']) && $_GET['app'] == 'authors'):

	$authors = new \app\Authors("authors.xlsx");
	$mySheet = new \app\Sheet("articles.xlsx");

	$authors->setArticles($mySheet->get());

	foreach ($authors->get() as $a):

		if (!isset($a['full_name'])):
			?>
			<h2 id="auth<?php echo $a['letter'] ?>"><?php echo $a['letter'] ?></h2>
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


/**
 * ==================================================
 * Articles
 */
if (isset($_GET['app']) && $_GET['app'] == 'articles'):

	$mySheet = new \app\Sheet("articles.xlsx");

	//	$authors = new \app\Authors("authors.xlsx");

	//	$authors->setArticles($mySheet->get());

	$i = 2;

	foreach ($mySheet->get() as $s):
		?>

		<p><?php echo $s['title']?> @</p>
		<p><?php echo $i?></p>
		<p><?php echo $mySheet->getCompiledAuthors($s['authors'])?></p>
		<p>+++</p>



	<?php
	$i = $i + 2;
	endforeach;
endif;
?>

<!--<pre>-->
<!--		--><?php ////print_r($mySheet->get()); ?>
<!--	</pre>-->