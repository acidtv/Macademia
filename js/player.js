var app = app || {};

(function() {
	var PlayerModel = Backbone.Model.extend({
		songid: '',
		currentsong: {},
		status: {},

		update: function() {
			$.getJSON('/api/mpd/status', function(response) {
				self = app.PlayerModel;
				self.status = response.data;
				self.trigger('player:update', self);
				self.checkNewSong(response.data);
			});
		},

		checkNewSong: function (data) {
			songid = data.playlist + data.songid;

			if (songid != this.songid) {
				this.songid = songid;
				$.getJSON('/api/mpd/currentsong', function(response) {
					app.PlayerModel.setNewSong(response.data);
				});
			}
		},

		setNewSong: function (data) {
			console.log('newsong');
			this.currentsong = data;
			this.trigger('player:newsong', this);
		}
	});

	app.PlayerModel = new PlayerModel();
}());

(function() {
	app.PlaylistSongModel = Backbone.Model.extend({
	});
}());

(function() {
	var PlaylistCollection = Backbone.Collection.extend({
		model: app.PlaylistSongModel,
		url: '/api/mpd/playlistinfo',

		initialize: function() {
		},

		parse: function(response) {
			return _.values(response.data);
		}
	});

	app.PlaylistCollection = new PlaylistCollection();
}());

(function() {
	app.PlaylistView = Backbone.View.extend({
		el: '#playlist',

		initialize: function() {
			this.listenTo(app.PlaylistCollection, 'reset', this.addAll);
			app.PlaylistCollection.fetch();
		},

		render: function() {
			// playlist stats
		},

		addOne: function(item) {
			view = new app.PlaylistItemView({model: item});
			this.$el.append(view.render().el);
		},

		addAll: function() {
			app.PlaylistCollection.each(this.addOne, this)
		}
	});
}());

(function() {
	app.PlaylistItemView = Backbone.View.extend({
		tagName: 'tr',

		template: _.template($('#playlist-item').html()),

		initialize: function() {
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});
}());

(function() {
	app.AppView = Backbone.View.extend({
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
