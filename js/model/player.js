var app = app || {};

(function() {
	var PlayerModel = Backbone.Model.extend({
		songid: '',
		playlistid: '',
		currentsong: {},
		status: {},

		initialize: function() {
			this.update();
		},

		update: function() {
			$.getJSON('/api/mpd/status', function(response) {
				self = app.PlayerModel;
				self.status = response.data;
				self.trigger('player:update', self);

				// update playlist first
				self.checkPlaylist(response.data);

				// then check if song has changed
				self.checkNewSong(response.data);
			});
		},

		checkNewSong: function(data) {
			if (this.songid != data.songid) {
				this.songid = data.songid;
				$.getJSON('/api/mpd/currentsong', function(response) {
					app.PlayerModel.setNewSong(response.data);
				});
			}
		},

		setNewSong: function(data) {
			console.log('newsong');
			this.currentsong = data;
			this.trigger('player:newsong', this);
		},

		checkPlaylist: function(data) {
			if (this.playlistid != data.playlist) {
				this.playlistid = data.playlist;
				this.trigger('player:newplaylist', this);
			}
		}
	});

	app.PlayerModel = new PlayerModel();
}());
