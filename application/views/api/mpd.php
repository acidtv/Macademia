<html>
<head>
	<title>MPD API Docs</title>
	<style>
	body {
		font-family: Arial;
	}

	.params .name {
		font-weight: bold;
	}

	.params .optional {
		color: #999;
	}
	</style>
</head>
<body>

	<h1>MPD API Docs</h1>
	<hr />

	<? foreach ($methods as $method): ?>
		<h2><?=$method['name']?></h2>
		<p><?=$method['doc_comment'][0]?></p>
		<? if ($method['params']): ?>
			<p>
				Parameters:
				<ul class="params">
					<? foreach ($method['params'] as $param): ?>
						<li>
							<span class="name"><?=$param->name?></span>
							<? if ($param->isDefaultValueAvailable()): ?>
								= <?=$param->getDefaultValue() ?>
								<span class="optional">optional</span>
							<? endif ?>
						</li>
					<? endforeach ?>
				</ul>
			</p>
		<? endif ?>
		<p>URL: <?=Route::url('api', array('controller' => 'mpd', 'action' => $method['name'])) ?></p>
	<? endforeach ?>

</body>
</html>