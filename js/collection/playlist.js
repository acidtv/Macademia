var app = app || {};

(function() {
	var PlaylistCollection = Backbone.Collection.extend({
		model: app.PlaylistSongModel,
		url: '/api/mpd/playlistinfo',

		initialize: function() {
			this.listenTo(app.PlayerModel, 'player:newsong', function(player) {
				this.markCurrentSong(player.currentsong.Id);
			});
			this.listenTo(app.PlayerModel, 'player:newplaylist', function(player) {
				this.fetch();
				this.markCurrentSong(player.currentsong.Id);
			});
		},

		parse: function(response) {
			return _.values(response.data);
		},

		markCurrentSong: function(songid) {
			this.get(songid).markAsCurrentSong();
		}
	});

	app.PlaylistCollection = new PlaylistCollection();
}());
