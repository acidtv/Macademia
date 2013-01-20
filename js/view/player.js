var app = app || {};

(function() {
	app.PlayerView = Backbone.View.extend({
		el: '#player',

		events: {
			'click #start': 'startPlayback'
		},

		initialize: function() {
			this.$progressBar = this.$('#progress #bar');

			this.listenTo(app.PlayerModel, 'player:update', this.updateProgress);
			this.listenTo(app.PlayerModel, 'player:newsong', this.updateSong);

			// start progress updater
			setInterval(function() { app.PlayerModel.update(); }, 1000);
		},

		render: function() {
		},

		startPlayback: function() {
			alert('starting playback');
		},

		updateProgress: function(player) {
			this.$progressBar.width(player.status.elapsed);
		},

		updateSong: function(player) {
			this.$('#artist').html(player.currentsong.Artist);
			this.$('#song').html(player.currentsong.Title);
		}
	})

}());
