<html>
<head>
	<link rel='stylesheet' href='/css/style.css' type='text/css' media='screen' />
</head>
<body>

	<h1>Macademia</h1> 

	<div id="player">
		<!-- <span id="start">Start playback</span> -->
		<div id="current">
			<span id="artist"></span> - <span id="song"></span>
		</div>
		<div id="progress">
			<div id="bar"></div>
		</div>
	</div>

	<hr />

	<table id="playlist"></table>

	<p>
		Visit the <a href="/api/mpd">API Docs</a>
	</p>

	<script type="text/template" id="playlist-item">
		<td><%= Artist %> - <%= Title %></td>
		<td id="time"><%= _Time %></td>
		<td><%= Album %></td>
	</script>
	<script language="javascript" src="/js/lib/underscore.min.js"></script>
	<script language="javascript" src="/js/lib/jquery.min.js"></script>
	<script language="javascript" src="/js/lib/backbone.js"></script>
	<script language="javascript" src="/js/player.js"></script>
	<script language="javascript">
		var app = app || {};

		$(function() {
			new app.AppView();
			new app.PlaylistView();
		});
	</script>
</body>
</html>